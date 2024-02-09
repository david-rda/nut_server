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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('email')->unique()->nullable();
            $table->string("company_name")->nullable();
            $table->string("identification_code")->unique()->nullable();
            $table->string("legal_address")->nullable(); // იურიდიული მისამართი
            $table->string("actual_address")->nullable(); // ფაქტობრივი მისამართი
            $table->string("personal_id")->unique()->nullable();
            $table->string("status")->default("pending")->nullable(); // , ["active", "inactive", "pending"]
            $table->string("permission")->default("company")->nullable(); // , ["coordinator", "company", "operator"]
            $table->string("mobile")->nullable();
            $table->string("password")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
