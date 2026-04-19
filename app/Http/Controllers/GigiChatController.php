<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GigiChatController extends Controller
{
    public function chat(Request $request): JsonResponse
    {
        $request->validate([
            'message' => ['required', 'string', 'max:2000'],
        ]);

        // Phase 7: Gemini + rulebook. Level-1 quick replies stub:
        $msg = mb_strtolower($request->input('message'));
        if (str_contains($msg, 'hi') || str_contains($msg, 'hello')) {
            return response()->json([
                'reply' => 'Hello from GicoGifts. Browse our gift boxes or tell me the occasion and budget.',
            ]);
        }

        return response()->json([
            'reply' => 'Thanks for your message. Full Gigi AI is coming in Phase 7. For now, visit the shop or contact us.',
        ]);
    }
}
