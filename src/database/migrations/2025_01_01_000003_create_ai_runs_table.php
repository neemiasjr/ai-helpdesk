<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ai_runs', function (Blueprint $table) {
            $table->id();

            $table->string('run_type', 64);
            $table->morphs('entity');
            $table->foreignId('requested_by')->nullable()->constrained('users');

            $table->string('provider', 32)->default('openai');
            $table->string('model', 64)->nullable();

            $table->longText('prompt')->nullable();
            $table->longText('response')->nullable();

            $table->string('status', 32)->default('queued'); // queued|running|success|failed
            $table->unsignedInteger('duration_ms')->nullable();

            $table->text('error_message')->nullable();
            $table->unsignedTinyInteger('attempt')->default(0);

            $table->timestamps();

            $table->index(['run_type', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_runs');
    }
};
