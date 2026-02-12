<?php

namespace App\Http\Controllers;

use App\Services\StreamerService;
use Illuminate\Http\JsonResponse;

class StreamerController extends Controller
{
    protected StreamerService $streamerService;

    public function __construct(StreamerService $streamerService)
    {
        $this->streamerService = $streamerService;
    }

    /**
     * Get list of all streamers
     */
    public function getStreamers(): JsonResponse
    {
        $streamers = $this->streamerService->getStreamers();

        return response()->json([
            'streamers' => $streamers,
            'count' => count($streamers),
        ]);
    }

    public function getStreamersWithDetails(): JsonResponse
    {
        $streamers = $this->streamerService->getStreamersWithDetails();

        return response()->json([
            'streamers' => $streamers,
            'count' => count($streamers),
        ]);
    }
}
