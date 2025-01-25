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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description');
            $table->unsignedBigInteger('venue_id'); // unsignedBigInteger kullanımı
            $table->date('start_date');
            $table->date('end_date');
            $eventStatusVlues = array_column(\App\Enums\EventsStatus::cases(),'value'); //ENUM YAPISI
            $table->enum('status',$eventStatusVlues)->default(\App\Enums\EventsStatus::INCOMING->value);
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
