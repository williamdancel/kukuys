<?php

// app/Services/TaryahanMatchService.php

namespace App\Services;

use App\Models\TaryahanMatch;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class TaryahanMatchService
{
    /**
     * Get all match records with pagination and filters
     */
    public function getAllMatches(array $filters = []): LengthAwarePaginator
    {
        $query = TaryahanMatch::query();

        // Apply search filter
        if (! empty($filters['search'])) {
            $query->search($filters['search']);
        }

        // Apply date range filter
        if (! empty($filters['date_from']) || ! empty($filters['date_to'])) {
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
     * Get a single match by ID
     */
    public function getMatchById(int $id): ?TaryahanMatch
    {
        return TaryahanMatch::find($id);
    }

    /**
     * Create a new match record
     */
    public function createMatch(array $data): TaryahanMatch
    {
        return DB::transaction(function () use ($data) {
            return TaryahanMatch::create($data);
        });
    }

    /**
     * Delete a match record
     */
    public function deleteMatch(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $match = TaryahanMatch::findOrFail($id);

            return $match->delete();
        });
    }

    /**
     * Get match statistics
     */
    public function getStatistics(): array
    {
        $totalMatches = TaryahanMatch::count();
        $teamAWins = TaryahanMatch::where('winner', 'team_a')->count();
        $teamBWins = TaryahanMatch::where('winner', 'team_b')->count();

        return [
            'total_matches' => $totalMatches,
            'team_a_wins' => $teamAWins,
            'team_b_wins' => $teamBWins,
            'team_a_win_rate' => $totalMatches > 0 ? round(($teamAWins / $totalMatches) * 100, 2) : 0,
            'team_b_win_rate' => $totalMatches > 0 ? round(($teamBWins / $totalMatches) * 100, 2) : 0,
            'unique_players' => $this->countUniquePlayers(),
            'most_common_captains' => $this->getMostCommonCaptains(),
        ];
    }

    /**
     * Count unique players across all matches
     */
    private function countUniquePlayers(): int
    {
        $teamAPlayers = TaryahanMatch::pluck('team_a_players')->flatten();
        $teamBPlayers = TaryahanMatch::pluck('team_b_players')->flatten();

        return $teamAPlayers->merge($teamBPlayers)->unique()->count();
    }

    /**
     * Get most common captains
     */
    private function getMostCommonCaptains(): array
    {
        $captains = TaryahanMatch::select('team_a_captain', 'team_b_captain')
            ->get()
            ->flatMap(function ($match) {
                return [$match->team_a_captain, $match->team_b_captain];
            })
            ->countBy()
            ->sortDesc()
            ->take(5)
            ->toArray();

        return $captains;
    }
}
