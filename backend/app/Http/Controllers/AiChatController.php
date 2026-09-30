<?php

namespace App\Http\Controllers;

use App\Services\GeminiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AiChatController extends Controller
{
    public function __construct(private GeminiService $gemini) {}

    public function chat(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'message' => 'required|string|max:500',
            'history' => 'nullable|array',
            'history.*.role' => 'required_with:history|in:user,model,assistant',
            'history.*.text' => 'required_with:history|string|max:1000',
        ]);

        $history = $validated['history'] ?? [];
        $result = $this->gemini->chat($validated['message'], $history);

        return response()->json($result);
    }
}
