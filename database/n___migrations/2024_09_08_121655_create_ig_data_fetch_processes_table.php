<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ig_data_fetch_processes', function (Blueprint $table) {
            $table->id();
            $table->enum('IDFP_status', ['processing', 'finished_fetching_ig_profile_from_instagram', 'finished_success', 'finished_error']);

            $table->foreignId('IDFP_ig_bussines_account')->constrained(
                table: 'ig_access_codes'
            );

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ig_data_fetch_processes');
    }
};
