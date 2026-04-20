<?php

namespace Tests\Feature;

use App\Services\Gigi\GigiChatService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class GigiChatTest extends TestCase
{
    use RefreshDatabase;

    public function test_hi_returns_level_one_greeting(): void
    {
        $response = $this->post(route('gigi.chat'), [
            'message' => 'Hello!',
        ]);

        $response->assertOk();
        $response->assertJsonStructure(['reply']);
        $this->assertStringContainsStringIgnoringCase('shop', $response->json('reply'));
    }

    public function test_refund_message_points_to_policy(): void
    {
        $response = $this->post(route('gigi.chat'), [
            'message' => 'What is your refund policy?',
        ]);

        $response->assertOk();
        $this->assertStringContainsString('/refund-policy', $response->json('reply'));
    }

    public function test_unknown_message_without_gemini_falls_back_gracefully(): void
    {
        config(['services.gemini.key' => null]);

        $response = $this->post(route('gigi.chat'), [
            'message' => 'zzzz obscure phrase no keywords here',
        ]);

        $response->assertOk();
        $this->assertNotEmpty($response->json('reply'));
    }

    public function test_gemini_reply_when_configured_and_http_succeeds(): void
    {
        config([
            'services.gemini.key' => 'test-key',
            'services.gemini.model' => 'gemini-test',
        ]);

        Http::fake([
            'https://generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                ['text' => 'For Diwali under ₹2,000, consider the [Mini Rajasthan Sampler](/product/mini-rajasthan-sampler).'],
                            ],
                        ],
                    ],
                ],
            ], 200),
        ]);

        // Http::fake is cleared between $this->post() and the app request, so exercise the service directly.
        $reply = app(GigiChatService::class)->respond('What gift should I buy for Diwali under 2000 rupees?');

        $this->assertStringContainsString('Mini Rajasthan', $reply);
        $this->assertGreaterThan(0, count(Http::recorded()));
    }

    public function test_logs_chat_when_gigi_log_chats_enabled(): void
    {
        config(['gigi.log_chats' => true, 'services.gemini.key' => null]);

        $this->post(route('gigi.chat'), [
            'message' => 'Hey there',
        ]);

        $this->assertDatabaseHas('gigi_chat_logs', [
            'reply_level' => 'l1',
        ]);
    }
}
