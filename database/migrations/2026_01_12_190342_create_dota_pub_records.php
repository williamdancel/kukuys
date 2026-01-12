<?php
// database/migrations/[timestamp]_create_dota_pub_records_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dota_pub_records', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('total_pubs');
            $table->integer('win');
            $table->integer('lose');
            $table->date('match_date');
            $table->timestamps();
            
            // Indexes for better performance
            $table->index('name');
            $table->index('match_date');
            $table->index(['name', 'match_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dota_pub_records');
    }
};