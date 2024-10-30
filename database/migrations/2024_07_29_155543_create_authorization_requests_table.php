<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('authorization_requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_id')->unique();
            $table->timestamps();
            $table->foreignId('user_id')->constrained(
                table: 'users',
            );
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('authorization_requests');
    }
};
