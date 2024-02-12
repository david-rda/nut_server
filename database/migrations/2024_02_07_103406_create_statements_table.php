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
        Schema::create('statements', function (Blueprint $table) {
            $table->id();
            $table->integer("user_id")->nullable();
            $table->integer("operator_id")->nullable();
            $table->string("status", 20)->nullable();
            $table->integer("attachement_id")->nullable();
            $table->string("overhead_number", 50)->nullable(); // ზედნადების ნომერი
            $table->date("overhead_date")->nullable(); // ზედნადების თარიღი
            $table->string("store_address")->nullable();
            $table->string("beneficiary_name", 50)->nullable();
            $table->string("card_number")->nullable(); // ბოლო 4 ციფრი
            $table->double("full_amount")->nullable();
            $table->text("comment")->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('statements');
    }
};
