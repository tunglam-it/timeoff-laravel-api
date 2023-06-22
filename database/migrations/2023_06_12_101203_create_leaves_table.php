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
        Schema::create('leaves', function (Blueprint $table) {
            $table->id();
            $table->integer('employee_id');
            $table->integer('type');
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->longText('reason');
            $table->float('estimate');
            $table->longText('comment')->nullable();
            $table->string('status')->default('3')->comment('1-chap thuan, 2-tu choi, 3-dang cho');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leaves');
    }
};
