<?php
// app/Services/DotaPubRecordService.php

namespace App\Services;

use App\Models\DotaPubRecord;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DotaPubRecordService
{
    /**
     * Get all records with pagination and filters
     */
    public function getAllRecords(array $filters = []): LengthAwarePaginator
    {
        $query = DotaPubRecord::query();
        
        // Apply search filter
        if (!empty($filters['search'])) {
            $query->search($filters['search']);
        }
        
        // Apply date range filter
        if (!empty($filters['date_from']) || !empty($filters['date_to'])) {
            $query->dateRange($filters['date_from'] ?? null, $filters['date_to'] ?? null);
        }
        
        // Apply sorting (default by latest)
        $sortField = $filters['sort_by'] ?? 'match_date';
        $sortDirection = $filters['sort_dir'] ?? 'desc';
        $query->orderBy($sortField, $sortDirection);
        
        // Paginate results
        $perPage = $filters['per_page'] ?? 20;
        
        return $query->paginate($perPage);
    }

    /**
     * Get a single record by ID
     */
    public function getRecordById(int $id): ?DotaPubRecord
    {
        return DotaPubRecord::find($id);
    }

    /**
     * Create a new record
     */
    public function createRecord(array $data): DotaPubRecord
    {
        // Validate win + lose equals total pubs
        $this->validateMatchCounts($data);
        
        return DB::transaction(function () use ($data) {
            return DotaPubRecord::create($data);
        });
    }

    /**
     * Update an existing record
     */
    public function updateRecord(int $id, array $data): DotaPubRecord
    {
        // Validate win + lose equals total pubs
        $this->validateMatchCounts($data);
        
        return DB::transaction(function () use ($id, $data) {
            $record = DotaPubRecord::findOrFail($id);
            $record->update($data);
            
            return $record->fresh();
        });
    }

    /**
     * Delete a record
     */
    public function deleteRecord(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $record = DotaPubRecord::findOrFail($id);
            return $record->delete();
        });
    }

    /**
     * Validate that win + lose equals total pubs
     */
    private function validateMatchCounts(array $data): void
    {
        $totalPubs = (int) ($data['total_pubs'] ?? 0);
        $win = (int) ($data['win'] ?? 0);
        $lose = (int) ($data['lose'] ?? 0);
        
        if (($win + $lose) !== $totalPubs) {
            throw ValidationException::withMessages([
                'total_pubs' => ['Win count + Lose count must equal Total Pubs']
            ]);
        }
    }

    /**
     * Get statistics for all records
     */
    public function getStatistics(): array
    {
        return [
            'total_records' => DotaPubRecord::count(),
            'total_matches' => DotaPubRecord::sum('total_pubs'),
            'total_wins' => DotaPubRecord::sum('win'),
            'total_losses' => DotaPubRecord::sum('lose'),
            'overall_win_rate' => DotaPubRecord::count() > 0 
                ? round((DotaPubRecord::sum('win') / DotaPubRecord::sum('total_pubs')) * 100, 2)
                : 0,
            'unique_players' => DotaPubRecord::distinct('name')->count('name'),
        ];
    }
}