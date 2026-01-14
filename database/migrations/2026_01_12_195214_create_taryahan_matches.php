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
            $table->json('team_a_players');
            $table->json('team_b_players');
            $table->string('team_a_captain');
            $table->string('team_b_captain');
            $table->enum('winner', ['team_a', 'team_b'])->nullable();
            $table->enum('game_type', ['dota2', 'cs2'])->default('cs2');
            $table->date('match_date');
            $table->timestamps();
            
            $table->index('team_a_name');
            $table->index('team_b_name');
            $table->index('match_date');
            $table->index('winner');
            $table->index('game_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('taryahan_matches');
    }
};