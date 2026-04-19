<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Filament\Resources\Orders\OrderResource;
use App\Mail\OrderConfirmed;
use App\Models\OrderStatusHistory;
use App\Services\Shipping\ShiprocketService;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;
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
