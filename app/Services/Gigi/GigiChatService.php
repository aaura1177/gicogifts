<?php

namespace App\Services\Gigi;

use App\Models\GigiChatLog;
use App\Models\Product;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;

class GigiChatService
{
    public function __construct(
        private GigiGeminiClient $gemini,
    ) {}

    public function respond(string $rawMessage): string
    {
        $msg = mb_strtolower(trim($rawMessage));
        $sanitized = $this->sanitizeForLog($rawMessage);

        $this->maybeNotifyPriceSensitivity($sanitized);

        $l1 = $this->matchLevel1($msg);
        if ($l1 !== null) {
            $this->maybeLog($sanitized, $l1, 'l1');

            return $l1;
        }

        $geminiText = $this->tryGemini($rawMessage);
        if ($geminiText !== null) {
            $this->maybeLog($sanitized, $geminiText, 'l2');

            return $geminiText;
        }

        $fallback = 'I can help with shipping times, returns, tracking, and gift ideas from our catalog. Browse curated boxes at /shop/boxes or tell me the occasion and budget in INR for tailored picks. For account-specific help, use Contact from the footer.';
        $this->maybeLog($sanitized, $fallback, 'fallback');

        return $fallback;
    }

    private function matchLevel1(string $msg): ?string
    {
        if (preg_match('/\b(refund|return(s)?)\b/i', $msg) || str_contains($msg, 'money back')) {
            return 'Returns for artisan goods follow our breakage-focused policy. Read the details at /refund-policy — or tell me what went wrong and I will point you to the right next step.';
        }

        if (preg_match('/\b(shipping|delivery|dispatch|courier)\b/i', $msg) || preg_match('/how\s+long.*\b(ship|deliver|arrive)/i', $msg)) {
            return 'We ship across India in about 3–7 business days, and international orders typically take 7–14 days. Flat ₹99 shipping in India, or free above ₹2,000. Full detail: /shipping-policy';
        }

        if (preg_match('/\b(track|tracking|awb)\b/i', $msg) || preg_match('/where\s+is\s+my\s+order/i', $msg)) {
            return 'Use the AWB from your shipping email: open /track/ on this site and add your AWB after it (example: /track/ABC123456789). You can also email us via Contact with your order number.';
        }

        if (preg_match('/\b(corporate|bulk|wedding\s+favou?rs?)\b/i', $msg)) {
            return 'For corporate gifting and wedding favours we put together volume quotes and custom notes. Start here: /corporate-gifting';
        }

        if (preg_match('/\b(hi|hello|hey|good\s+(morning|afternoon|evening))\b/i', $msg)) {
            return 'Hello from GicoGifts — curated artisan gift boxes from Rajasthan. Browse gift boxes at /shop/boxes, or tell me the occasion and budget and I will suggest a couple of matches.';
        }

        return null;
    }

    private function tryGemini(string $userMessage): ?string
    {
        if (! $this->gemini->isConfigured()) {
            return null;
        }

        $rulebookPath = base_path('docs/GIGI_AI_RULEBOOK.md');
        $rulebook = File::exists($rulebookPath)
            ? (string) File::get($rulebookPath)
            : 'Follow brand voice: warm, knowledgeable, never invent catalog facts.';

        $discountNote = config('gigi.allow_discount_mentions')
            ? 'Discount codes may be mentioned when accurate.'
            : 'Do not offer coupons, personalised discounts, or haggling.';

        $catalog = Product::query()
            ->where('is_active', true)
            ->whereNotNull('published_at')
            ->orderByDesc('is_featured')
            ->orderBy('sort_order')
            ->limit(30)
            ->get(['name', 'slug', 'price_inr', 'short_description', 'is_box'])
            ->values();

        $catalogJson = mb_substr($catalog->toJson(JSON_UNESCAPED_UNICODE), 0, 12000);

        $system = $rulebook."\n\n".$discountNote."\n\nActive catalog (JSON array; use only these products with /product/{slug} links):\n".$catalogJson;

        $result = $this->gemini->generate($userMessage, $system);
        if ($result['ok'] && is_string($result['text'])) {
            return $result['text'];
        }

        return null;
    }

    private function maybeNotifyPriceSensitivity(string $sanitized): void
    {
        if (! preg_match('/\b(expensive|too pricey|price too high|too much|discount|coupon|cheaper|lower price)\b/i', $sanitized)) {
            return;
        }

        $url = config('services.slack.webhook_url');
        if (! is_string($url) || $url === '') {
            return;
        }

        $snippet = mb_substr($sanitized, 0, 400);

        try {
            Http::asJson()->timeout(5)->post($url, [
                'text' => "Gigi — price / discount signal\n```\n{$snippet}\n```",
            ]);
        } catch (\Throwable) {
            //
        }
    }

    private function maybeLog(string $sanitized, string $reply, string $level): void
    {
        if (! config('gigi.log_chats')) {
            return;
        }

        try {
            GigiChatLog::query()->create([
                'message_sanitized' => $sanitized,
                'reply' => $reply,
                'reply_level' => $level,
                'meta' => null,
            ]);
        } catch (\Throwable) {
            //
        }
    }

    private function sanitizeForLog(string $message): string
    {
        $s = preg_replace('/[a-z0-9._%+\-]+@[a-z0-9.\-]+\.[a-z]{2,}/i', '[email]', $message) ?? $message;
        $s = preg_replace('/\+?\d[\d\s\-]{8,}\d/', '[phone]', $s) ?? $s;

        return mb_substr(trim($s), 0, 4000);
    }
}
