<?php
// app/Models/DotaPubRecord.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DotaPubRecord extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'total_pubs',
        'win',
        'lose',
        'match_date',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'match_date' => 'date',
        'total_pubs' => 'integer',
        'win' => 'integer',
        'lose' => 'integer',
    ];

    /**
     * Scope for filtering by search term
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'like', '%' . $search . '%');
    }

    /**
     * Scope for filtering by date range
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
     * Calculate win rate attribute
     */
    public function getWinRateAttribute(): float
    {
        if ($this->total_pubs === 0) {
            return 0;
        }
        
        return ($this->win / $this->total_pubs) * 100;
    }

    /**
     * Validation rules
     */
    public static function rules($id = null): array
    {
        return [
            'name' => 'required|string|max:255',
            'total_pubs' => 'required|integer|min:1',
            'win' => 'required|integer|min:0',
            'lose' => 'required|integer|min:0',
            'match_date' => 'required|date',
        ];
    }

    /**
     * Validation messages
     */
    public static function messages(): array
    {
        return [
            'name.required' => 'Name is required',
            'total_pubs.required' => 'Total pubs is required',
            'total_pubs.min' => 'Total pubs must be at least 1',
            'win.required' => 'Win count is required',
            'win.min' => 'Win count cannot be negative',
            'lose.required' => 'Lose count is required',
            'lose.min' => 'Lose count cannot be negative',
            'match_date.required' => 'Match date is required',
            'match_date.date' => 'Match date must be a valid date',
        ];
    }
}