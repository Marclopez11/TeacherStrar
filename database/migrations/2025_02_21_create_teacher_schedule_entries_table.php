<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('teacher_schedule_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('time_slot_id')->constrained()->onDelete('cascade');
            $table->string('day');
            $table->string('subject');
            $table->timestamps();

            // Un profesor solo puede tener una entrada por franja horaria y día
            $table->unique(['user_id', 'time_slot_id', 'day']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('teacher_schedule_entries');
    }
};
