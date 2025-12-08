<?php

namespace App\Http\Controllers;

use App\Services\WordbaseService;
use Illuminate\Http\Request;
use Throwable;

class WordbaseController extends Controller
{
    /**
     * Handle wordbase import requests
     *
     * @param \Illuminate\Http\Request $request
     * @param WordbaseService $importer
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function submit(Request $request, WordbaseService $importer)
    {
        $url = $request->validate([
            'url' => ['required', 'url'],
        ])['url'];

        try {
            $processed = $importer->run($url);
        } catch (Throwable $throwable) {
            report($throwable);

            return response()->json([
                'error' => $throwable->getMessage(),
            ]);
        }

        return response()->json([
            'message' => 'Import completed successfully.',
            'processed' => $processed,
        ]);
    }
}
