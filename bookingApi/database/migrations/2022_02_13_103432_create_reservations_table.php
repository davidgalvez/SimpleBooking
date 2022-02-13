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
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
			$table->foreignId('apartment_id')->constrained('apartments');
            $table->string('name', 200);
            $table->date('birthdate');
            $table->boolean('confirmed')->nullable()->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
		 Schema::table('reservations', function (Blueprint $table) {
            $table->dropForeign(['apartment_id']);           
        });
        Schema::dropIfExists('reservations');
    }
};
