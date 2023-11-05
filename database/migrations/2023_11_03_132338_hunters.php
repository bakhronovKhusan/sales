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
        Schema::create('hunters_records', function (Blueprint $table){
            $table->bigIncrements('id');
            $table->enum('is_cheque', [0, 1])->default(0);
            $table->enum('is_roadmap', [0, 1])->default(0);
            $table->enum('is_online', [0, 1])->default(0);
            $table->enum('status', [0, 1])->default(0);
            $table->integer('group_student_id');
            $table->integer('offline_hunter_id')->nullable();
            $table->integer('online_hunter_id')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hunters_records');
    }
};
