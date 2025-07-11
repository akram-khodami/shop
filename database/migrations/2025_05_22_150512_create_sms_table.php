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
        Schema::create('sms', function (Blueprint $table) {
            $table->id();
            $table->string('code', 255);
            $table->string('receptor', 11)->index();
            $table->timestamp('expired_at');
            $table->enum('serviceName', ['kavenegar'])->default('kavenegar');
            $table->enum('serviceType', ['sms', 'call'])->default('sms');
            $table->enum('status', ['pending', 'retry', 'success', 'failed', 'cancelled'])->default('pending');
            $table->boolean('used')->default(false);
            $table->ipAddress('ip')->nullable();
            $table->timestamps();
            //Composite index for performance optimization
            $table->index(['receptor', 'code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sms');
    }
};
