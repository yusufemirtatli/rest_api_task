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
        Schema::create('seats', function (Blueprint $table) {
            $table->id();
            $table->integer('venue_id');
            $table->string('section');
            $table->integer('row');
            $table->string('number');
            $eventStatusVlues = array_column(\App\Enums\SeatStatus::cases(),'value'); //ENUM YAPISI
            $table->enum('status',$eventStatusVlues)->default(\App\Enums\SeatStatus::EMPTY->value);
            $table->integer('price');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seats');
    }
};
