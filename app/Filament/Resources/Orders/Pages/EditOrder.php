<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Filament\Resources\Orders\OrderResource;
use App\Mail\OrderConfirmed;
use App\Models\Order;
use App\Models\Payment;
use App\Models\OrderStatusHistory;
use App\Services\Payments\RazorpayService;
use App\Services\Shipping\ShiprocketService;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('markPacked')
                ->label('Mark packed')
                ->icon(Heroicon::OutlinedArchiveBox)
                ->visible(fn (): bool => $this->record->status === 'paid' && $this->record->packed_at === null)
                ->requiresConfirmation()
                ->action(function (): void {
                    $this->record->update(['packed_at' => now()]);
                    OrderStatusHistory::query()->create([
                        'order_id' => $this->record->id,
                        'status' => 'packed',
                        'note' => 'Marked packed from admin',
                        'created_by' => auth()->id(),
                    ]);
                    Notification::make()->title('Order marked packed')->success()->send();
                }),
            Action::make('resendInvoice')
                ->label('Resend invoice email')
                ->icon(Heroicon::OutlinedEnvelope)
                ->visible(fn (): bool => $this->record->status === 'paid')
                ->requiresConfirmation()
                ->action(function (): void {
                    $order = $this->record->fresh(['items']);
                    Mail::to($order->email)->send(new OrderConfirmed($order));
                    Notification::make()->title('Invoice email sent')->success()->send();
                }),
            Action::make('cancelRefund')
                ->label('Cancel & refund')
                ->icon(Heroicon::OutlinedArrowPathRoundedSquare)
                ->color('danger')
                ->visible(fn (): bool => $this->record->status === 'paid')
                ->form([
                    TextInput::make('amount_inr')
                        ->label('Refund amount (INR)')
                        ->numeric()
                        ->minValue(1)
                        ->helperText('Leave blank to refund full payment amount.')
                        ->default(null),
                    Textarea::make('note')
                        ->label('Reason / note')
                        ->rows(2)
                        ->maxLength(500)
                        ->default(null),
                ])
                ->requiresConfirmation()
                ->action(function (array $data, RazorpayService $razorpay): void {
                    $order = $this->record->fresh(['payments']);
                    if ($order->payment_gateway !== 'razorpay') {
                        Notification::make()->title('Only Razorpay refunds are automated')->danger()->send();

                        return;
                    }
                    if (! $razorpay->isConfigured()) {
                        Notification::make()->title('Razorpay is not configured')->danger()->send();

                        return;
                    }
                    if (! is_string($order->razorpay_payment_id) || $order->razorpay_payment_id === '') {
                        Notification::make()->title('Missing Razorpay payment id')->danger()->send();

                        return;
                    }

                    $amountInr = isset($data['amount_inr']) && $data['amount_inr'] !== null && $data['amount_inr'] !== ''
                        ? (float) $data['amount_inr']
                        : null;
                    $fullAmount = (float) $order->total_inr;
                    if ($amountInr !== null && ($amountInr <= 0 || $amountInr > $fullAmount)) {
                        Notification::make()->title('Refund amount must be between 1 and order total')->danger()->send();

                        return;
                    }
                    $amountPaise = $amountInr !== null ? (int) round($amountInr * 100) : null;
                    $note = trim((string) ($data['note'] ?? ''));

                    try {
                        $refund = $razorpay->refundPayment(
                            $order->razorpay_payment_id,
                            $amountPaise,
                            [
                                'order_id' => (string) $order->id,
                                'order_number' => $order->order_number,
                                'reason' => $note !== '' ? $note : 'Admin refund',
                            ],
                        );
                    } catch (\Throwable $e) {
                        Notification::make()
                            ->title('Refund failed')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();

                        return;
                    }

                    DB::transaction(function () use ($order, $refund, $note, $amountInr): void {
                        $locked = Order::query()->whereKey($order->id)->lockForUpdate()->first();
                        if (! $locked) {
                            return;
                        }

                        $locked->update(['status' => 'refunded']);

                        $captured = Payment::query()
                            ->where('order_id', $locked->id)
                            ->where('gateway', 'razorpay')
                            ->where('status', 'captured')
                            ->latest()
                            ->first();

                        if ($captured) {
                            $raw = is_array($captured->raw_response) ? $captured->raw_response : [];
                            $raw['refund'] = $refund;
                            $captured->update([
                                'status' => 'refunded',
                                'raw_response' => $raw,
                            ]);
                        }

                        OrderStatusHistory::query()->create([
                            'order_id' => $locked->id,
                            'status' => 'refunded',
                            'note' => $note !== '' ? $note : ('Refunded from admin'.($amountInr ? ' (₹'.number_format($amountInr, 2).')' : '')),
                            'created_by' => auth()->id(),
                        ]);
                    });

                    Notification::make()->title('Order refunded')->success()->send();
                    $this->record = $this->record->fresh();
                }),
            Action::make('downloadPickList')
                ->label('Print pick list')
                ->icon(Heroicon::OutlinedDocumentText)
                ->visible(fn (): bool => $this->record->items()->exists())
                ->action(function (): mixed {
                    $order = $this->record->fresh(['items']);

                    return Pdf::loadView('pdfs.pick-list', [
                        'order' => $order,
                        'legalLine' => config('gicogifts.legal_line'),
                    ])
                        ->setPaper('a4')
                        ->download('pick-list-'.$order->order_number.'.pdf');
                }),
            Action::make('downloadShippingLabel')
                ->label('Shipping label (Shiprocket)')
                ->icon(Heroicon::OutlinedArrowDownTray)
                ->visible(function (): bool {
                    $s = $this->record->shipments()->first();

                    return $s !== null && $s->shiprocket_shipment_id !== null && $s->shiprocket_shipment_id !== '';
                })
                ->action(function (ShiprocketService $shiprocket): ?\Illuminate\Http\RedirectResponse {
                    $shipment = $this->record->shipments()->first();
                    if ($shipment === null || $shipment->shiprocket_shipment_id === null || $shipment->shiprocket_shipment_id === '') {
                        Notification::make()->title('No Shiprocket shipment')->danger()->send();

                        return null;
                    }
                    if (! $shiprocket->isConfigured()) {
                        Notification::make()->title('Shiprocket is not configured')->danger()->send();

                        return null;
                    }
                    $url = $shiprocket->fetchShippingLabelUrl((string) $shipment->shiprocket_shipment_id);
                    if ($url === null) {
                        Notification::make()->title('Could not generate label')->body('Check Shiprocket dashboard or logs.')->danger()->send();

                        return null;
                    }
                    $shipment->update(['label_pdf_url' => $url]);

                    return redirect()->away($url);
                }),
            DeleteAction::make(),
        ];
    }
}
