<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Миграция на создание таблицы браузеров и устройств на конкретных IP.
 */
class CreateIpsInfoTable extends Migration
{
    public function up(): void
    {
        Schema::create('ips_devices', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('ip');
            $table->string('browser');
            $table->string('os');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ips_devices');
    }
}
