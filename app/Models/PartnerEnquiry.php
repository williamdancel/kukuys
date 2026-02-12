<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartnerEnquiry extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'company',
        'message',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get masked email for display (protects user privacy)
     */
    public function getMaskedEmailAttribute(): string
    {
        $email = $this->email;
        $parts = explode('@', $email);

        if (count($parts) === 2) {
            $username = $parts[0];
            $domain = $parts[1];

            // Show first 2 chars, then mask the rest of username
            $maskedUsername = substr($username, 0, 2).str_repeat('*', max(0, strlen($username) - 2));

            return $maskedUsername.'@'.$domain;
        }

        return $email;
    }
}
