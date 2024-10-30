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
        Schema::create('ig_access_codes', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('IG_APP_SCOPED_ID');
            $table->string('IG_USERNAME');
            $table->string('short_lived_access_token');
            $table->string('long_lived_access_token')->nullable();
            $table->string('long_lived_expires_in')->nullable();
            $table->string('permissions');
            $table->enum('webhook_status', ['active', 'inactive'])->default('inactive');

            $table->foreignId('user_id')->constrained(
                table: 'users'
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
        Schema::dropIfExists('clio_access_codes');
    }
};
