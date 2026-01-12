<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('partner_enquiries', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->string('email', 50);
            $table->string('company', 50);
            $table->text('message');
            $table->timestamps();
            
            // Indexes for performance
            $table->index('email');
            $table->index('created_at');
            $table->index(['created_at', 'email']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('partner_enquiries');
    }
};