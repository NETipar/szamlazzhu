<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(config('szamlazzhu.session.table', 'szamlazzhu_sessions'), function (Blueprint $table) {
            $table->string('key')->primary();
            $table->text('value');
            $table->timestamp('expires_at')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(config('szamlazzhu.session.table', 'szamlazzhu_sessions'));
    }
};
