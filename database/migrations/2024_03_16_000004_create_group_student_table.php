<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('group_student', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            // Un estudiante solo puede estar una vez en cada grupo
            $table->unique(['group_id', 'student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('group_student');
    }
};
