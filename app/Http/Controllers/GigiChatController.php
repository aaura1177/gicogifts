<?php

namespace App\Http\Controllers;

use App\Services\Gigi\GigiChatService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class GigiChatController extends Controller
{
    public function chat(Request $request, GigiChatService $gigi): JsonResponse
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'max:2000'],
        ]);

        try {
            $reply = $gigi->respond($validated['message']);
        } catch (Throwable $e) {
            report($e);

            return response()->json([
                'reply' => 'I could not answer that just now. Please try again in a moment, or use the Contact page for help.',
            ]);
        }

        return response()->json([
            'reply' => $reply,
        ]);
    }
}
