<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaryahanMatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'team_a_name',
        'team_b_name',
        'team_a_players',
        'team_b_players',
        'team_a_captain',
        'team_b_captain',
        'winner',
        'game_type',
        'match_date',
    ];

    protected $casts = [
        'team_a_players' => 'array',
        'team_b_players' => 'array',
        'match_date' => 'date',
    ];

    /**
     * Scope for searching matches
     */
    public function scopeSearch($query, $search)
    {
        if (! $search) {
            return $query;
        }

        return $query->where(function ($q) use ($search) {
            $q->where('team_a_name', 'like', '%'.$search.'%')
                ->orWhere('team_b_name', 'like', '%'.$search.'%')
                ->orWhere('team_a_captain', 'like', '%'.$search.'%')
                ->orWhere('team_b_captain', 'like', '%'.$search.'%')
                ->orWhereJsonContains('team_a_players', $search)
                ->orWhereJsonContains('team_b_players', $search);
        });
    }

    /**
     * Scope for date range filtering
     */
    public function scopeDateRange($query, $dateFrom, $dateTo)
    {
        if ($dateFrom) {
            $query->whereDate('match_date', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->whereDate('match_date', '<=', $dateTo);
        }

        return $query;
    }

    /**
     * Get the winning team name
     */
    public function getWinningTeamAttribute(): string
    {
        return $this->winner === 'team_a' ? $this->team_a_name : $this->team_b_name;
    }

    /**
     * Get the losing team name
     */
    public function getLosingTeamAttribute(): string
    {
        return $this->winner === 'team_a' ? $this->team_b_name : $this->team_a_name;
    }

    /**
     * Validation rules
     */
    public static function rules(): array
    {
        return [
            'team_a_name' => 'required|string|max:255',
            'team_b_name' => 'required|string|max:255',
            'team_a_players' => 'required|array|size:5',
            'team_a_players.*' => 'required|string|max:255',
            'team_b_players' => 'required|array|size:5',
            'team_b_players.*' => 'required|string|max:255',
            'team_a_captain' => 'required|string|max:255',
            'team_b_captain' => 'required|string|max:255',
            'winner' => 'nullable|in:team_a,team_b',
            'game_type' => 'required|in:dota2,cs2',
            'match_date' => 'required|date',
        ];
    }

    /**
     * Validation messages
     */
    public static function messages(): array
    {
        return [
            'team_a_players.size' => 'Team A must have exactly 5 players',
            'team_b_players.size' => 'Team B must have exactly 5 players',
            'team_a_players.*.required' => 'Each Team A player name is required',
            'team_b_players.*.required' => 'Each Team B player name is required',
        ];
    }

    /**
     * Validation rules for updating winner
     */
    public static function winnerRules(): array
    {
        return [
            'winner' => 'required|in:team_a,team_b', // Required only when updating
        ];
    }
}
