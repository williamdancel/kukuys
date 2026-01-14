<?php
// app/Http/Controllers/Api/TaryahanMatchController.php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\TaryahanMatch;
use App\Services\TaryahanMatchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TaryahanMatchController extends Controller
{
    protected TaryahanMatchService $matchService;

    public function __construct(TaryahanMatchService $matchService)
    {
        $this->matchService = $matchService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $filters = [
                'search' => $request->input('search'),
                'date_from' => $request->input('date_from'),
                'date_to' => $request->input('date_to'),
                'sort_by' => $request->input('sort_by', 'match_date'),
                'sort_dir' => $request->input('sort_dir', 'desc'),
                'per_page' => $request->input('per_page', 20),
            ];

            $matches = $this->matchService->getAllMatches($filters);

            return response()->json([
                'success' => true,
                'data' => $matches,
                'message' => 'Matches retrieved successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve matches',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), TaryahanMatch::rules(), TaryahanMatch::messages());

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $data = $validator->validated();
            
            // Ensure captain is in their respective team
            if (!in_array($data['team_a_captain'], $data['team_a_players'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => [
                        'team_a_captain' => ['Captain must be a player in Team A']
                    ],
                ], 422);
            }
            
            if (!in_array($data['team_b_captain'], $data['team_b_players'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => [
                        'team_b_captain' => ['Captain must be a player in Team B']
                    ],
                ], 422);
            }
            
            // Winner can be null initially
            $data['winner'] = $data['winner'] ?? null;
            
            $match = $this->matchService->createMatch($data);

            return response()->json([
                'success' => true,
                'data' => $match,
                'message' => 'Match created successfully',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create match',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): JsonResponse
    {
        try {
            $match = $this->matchService->getMatchById($id);

            if (!$match) {
                return response()->json([
                    'success' => false,
                    'message' => 'Match not found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $match,
                'message' => 'Match retrieved successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve match',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $result = $this->matchService->deleteMatch($id);

            if (!$result) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete match',
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'Match deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete match',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the winner of a match
     */
    public function updateWinner(Request $request, int $id): JsonResponse
    {
        // Use winner-specific validation rules
        $validator = Validator::make($request->all(), TaryahanMatch::winnerRules());

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $match = TaryahanMatch::find($id);
            
            if (!$match) {
                return response()->json([
                    'success' => false,
                    'message' => 'Match not found',
                ], 404);
            }
            
            $match->winner = $request->winner;
            $match->save();

            return response()->json([
                'success' => true,
                'data' => $match,
                'message' => 'Winner updated successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update winner',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get match statistics
     */
    public function statistics(): JsonResponse
    {
        try {
            $stats = $this->matchService->getStatistics();

            return response()->json([
                'success' => true,
                'data' => $stats,
                'message' => 'Statistics retrieved successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve statistics',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}