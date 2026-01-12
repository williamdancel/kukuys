<?php
// database/migrations/[timestamp]_create_taryahan_matches_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('taryahan_matches', function (Blueprint $table) {
            $table->id();
            $table->string('team_a_name');
            $table->string('team_b_name');
            $table->json('team_a_players'); // Array of player names
            $table->json('team_b_players'); // Array of player names
            $table->string('team_a_captain');
            $table->string('team_b_captain');
            $table->enum('winner', ['team_a', 'team_b']);
            $table->date('match_date');
            $table->timestamps();
            
            // Indexes for better performance
            $table->index('team_a_name');
            $table->index('team_b_name');
            $table->index('match_date');
            $table->index('winner');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('taryahan_matches');
    }
};