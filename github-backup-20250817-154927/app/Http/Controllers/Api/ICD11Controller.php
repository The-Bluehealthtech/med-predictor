<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ICD11Service;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ICD11Controller extends Controller
{
    protected $icd11Service;

    public function __construct(ICD11Service $icd11Service)
    {
        $this->icd11Service = $icd11Service;
    }

    /**
     * Search for ICD-11 codes
     */
    public function search(Request $request): JsonResponse
    {
        $request->validate([
            'query' => 'required|string|min:2|max:255',
            'language' => 'string|in:en,fr,es,ar,zh,ru',
            'limit' => 'integer|min:1|max:50'
        ]);

        $query = $request->input('query');
        $language = $request->input('language', 'en');
        $limit = $request->input('limit', 10);

        $result = $this->icd11Service->search($query, $language, $limit);

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'data' => $result['results'],
                'total' => $result['total'],
                'query' => $query
            ]);
        }

        return response()->json([
            'success' => false,
            'error' => $result['error']
        ], 500);
    }

    /**
     * Get specific ICD-11 code details
     */
    public function getCode(Request $request, string $code): JsonResponse
    {
        $request->validate([
            'language' => 'string|in:en,fr,es,ar,zh,ru'
        ]);

        $language = $request->input('language', 'en');

        $result = $this->icd11Service->getCode($code, $language);

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'data' => $result
            ]);
        }

        return response()->json([
            'success' => false,
            'error' => $result['error']
        ], 404);
    }

    /**
     * Get ICD-11 chapters
     */
    public function getChapters(Request $request): JsonResponse
    {
        $request->validate([
            'language' => 'string|in:en,fr,es,ar,zh,ru'
        ]);

        $language = $request->input('language', 'en');

        $result = $this->icd11Service->getChapters($language);

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'data' => $result['chapters']
            ]);
        }

        return response()->json([
            'success' => false,
            'error' => $result['error']
        ], 500);
    }

    /**
     * Health check for ICD-11 API
     */
    public function health(): JsonResponse
    {
        $result = $this->icd11Service->search('diabetes', 'en', 1);

        return response()->json([
            'success' => $result['success'],
            'status' => $result['success'] ? 'healthy' : 'unhealthy',
            'timestamp' => now()->toISOString()
        ]);
    }
} 