<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chart_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chart_id')->constrained()->cascadeOnDelete();
            $table->foreignId('song_id')->constrained()->restrictOnDelete();
            $table->unsignedInteger('rank');
            $table->unsignedInteger('vote_count');
            $table->unsignedInteger('previous_rank')->nullable();
            $table->integer('movement')->nullable();
            $table->unsignedInteger('peak_rank');
            $table->unsignedInteger('charting_periods');
            $table->timestamps();

            $table->unique(['chart_id', 'song_id']);
            $table->unique(['chart_id', 'rank']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chart_entries');
    }
};
