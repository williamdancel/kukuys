<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\DotaPubRecord;
use App\Services\DotaPubRecordService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DotaPubRecordController extends Controller
{
    protected DotaPubRecordService $recordService;

    public function __construct(DotaPubRecordService $recordService)
    {
        $this->recordService = $recordService;
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

            $records = $this->recordService->getAllRecords($filters);

            return response()->json([
                'success' => true,
                'data' => $records,
                'message' => 'Records retrieved successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve records',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), DotaPubRecord::rules(), DotaPubRecord::messages());

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $record = $this->recordService->createRecord($validator->validated());

            return response()->json([
                'success' => true,
                'data' => $record,
                'message' => 'Record created successfully',
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create record',
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
            $record = $this->recordService->getRecordById($id);

            if (!$record) {
                return response()->json([
                    'success' => false,
                    'message' => 'Record not found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $record,
                'message' => 'Record retrieved successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve record',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $validator = Validator::make($request->all(), DotaPubRecord::rules($id), DotaPubRecord::messages());

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $record = $this->recordService->updateRecord($id, $validator->validated());

            return response()->json([
                'success' => true,
                'data' => $record,
                'message' => 'Record updated successfully',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update record',
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
            $result = $this->recordService->deleteRecord($id);

            if (!$result) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete record',
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'Record deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete record',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get statistics for all records
     */
    public function statistics(): JsonResponse
    {
        try {
            $stats = $this->recordService->getStatistics();

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