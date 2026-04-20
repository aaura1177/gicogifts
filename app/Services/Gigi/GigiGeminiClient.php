<?php

namespace App\Services\Gigi;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GigiGeminiClient
{
    public function isConfigured(): bool
    {
        $key = config('services.gemini.key');

        return is_string($key) && $key !== '';
    }

    /**
     * @return array{ok: bool, text: ?string, error: ?string}
     */
    public function generate(string $userMessage, string $systemInstruction): array
    {
        if (! $this->isConfigured()) {
            return ['ok' => false, 'text' => null, 'error' => 'missing_key'];
        }

        $model = (string) config('services.gemini.model', 'gemini-2.5-flash');
        $key = (string) config('services.gemini.key');
        $url = sprintf(
            'https://generativelanguage.googleapis.com/v1beta/models/%s:generateContent',
            rawurlencode($model)
        );

        try {
            $response = Http::timeout(55)
                ->acceptJson()
                ->withQueryParameters(['key' => $key])
                ->post($url, [
                    'contents' => [
                        [
                            'role' => 'user',
                            'parts' => [['text' => $userMessage]],
                        ],
                    ],
                    'systemInstruction' => [
                        'parts' => [['text' => $systemInstruction]],
                    ],
                    'generationConfig' => [
                        'maxOutputTokens' => 1024,
                        'temperature' => 0.65,
                    ],
                ]);
        } catch (\Throwable $e) {
            Log::warning('Gigi Gemini request failed', ['message' => $e->getMessage()]);

            return ['ok' => false, 'text' => null, 'error' => 'request_exception'];
        }

        if (! $response->successful()) {
            Log::warning('Gigi Gemini HTTP error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return ['ok' => false, 'text' => null, 'error' => 'http_'.$response->status()];
        }

        $text = data_get($response->json(), 'candidates.0.content.parts.0.text');
        if (! is_string($text) || trim($text) === '') {
            return ['ok' => false, 'text' => null, 'error' => 'empty_candidate'];
        }

        return ['ok' => true, 'text' => trim($text), 'error' => null];
    }
}
