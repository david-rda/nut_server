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
        Schema::create('change_operator_logs', function (Blueprint $table) {
            $table->id();
            $table->integer("statement_id");
            $table->integer("operator_before");
            $table->integer("operator_after");
            $table->integer("coordinator_id");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('change_operator_logs');
    }
};
