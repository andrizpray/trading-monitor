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
        Schema::create('monitor_snapshots', function (Blueprint $table) {
            $table->id();
            $table->date('snapshot_date')->unique();
            // User metrics
            $table->unsignedInteger('total_users')->default(0);
            $table->unsignedInteger('active_users_today')->default(0);
            $table->unsignedInteger('active_users_week')->default(0);
            $table->unsignedInteger('active_users_month')->default(0);
            $table->unsignedInteger('new_users_today')->default(0);
            // Trading metrics
            $table->unsignedInteger('total_entries')->default(0);
            $table->decimal('total_pnl', 20, 2)->default(0);
            $table->decimal('avg_pnl', 20, 2)->default(0);
            $table->decimal('win_rate', 5, 2)->default(0);
            // Server metrics (from Prometheus)
            $table->decimal('cpu_percent', 5, 2)->default(0);
            $table->decimal('ram_percent', 5, 2)->default(0);
            $table->decimal('disk_percent', 5, 2)->default(0);
            $table->decimal('disk_used_gb', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monitor_snapshots');
    }
};
