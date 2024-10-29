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
        Schema::create('user_registers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('CPF');
            $table->string('CPF_hash');
            $table->boolean('adulthood')->default(value:0);
            $table->foreignId('fk_id_photo')->nullable()->constrained('info_participations')->onUpdate('cascade');
            $table->foreignId('fk_id_session')->nullable()->constrained('session')->onUpdate('cascade');
            $table->string('responsible_name')->nullable();
            $table->string('responsible_cpf')->nullable();
            $table->string('responsible_cpf_hash')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_registers');
    }
};