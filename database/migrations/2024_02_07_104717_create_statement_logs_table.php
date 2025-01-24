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
        Schema::create('statement_logs', function (Blueprint $table) {
            $table->id();
            $table->integer("statement_id")->nullable();
            $table->integer("user_id")->nullable();
            $table->integer("operator_id")->nullable();
            $table->text("comment")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('statement_logs');
    }
};
