<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Миграция на добавление таблицы переходов.
 */
class CreateTransitionsTable extends Migration
{
    public function up(): void
    {
        Schema::create('transitions', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->timestamp('occurred_at');
            $table->string('ip');
            $table->string('url_from');
            $table->string('url_to');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transitions');
    }
}
