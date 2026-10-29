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
        Schema::create('ages', function (Blueprint $table) {
            $table->unsignedInteger('id')->primary();
            $table->string('slug')->unique();
            $table->string('name');
            $table->string('period');
            $table->string('subtitle');
            $table->text('paradigm_shift');
            $table->string('color', 20)->default('#38bdf8');
            $table->string('bg_glow', 50)->default('rgba(56, 189, 248, 0.15)');
            $table->timestamps();
        });

        Schema::create('people', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('name');
            $table->integer('born_year')->nullable();
            $table->integer('died_year')->nullable();
            $table->string('lifespan_display')->nullable();
            $table->string('nationality')->nullable();
            $table->text('bio')->nullable();
            $table->timestamps();
        });

        Schema::create('experiments', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('title');
            $table->string('type'); // observation, experiment, theory, invention
            $table->integer('year');
            $table->string('display_year');
            $table->unsignedInteger('age_id');
            $table->string('person_id')->nullable();
            $table->string('person_name_override')->nullable();
            $table->text('prior_knowledge');
            $table->text('question');
            $table->text('method');
            $table->text('conclusion');
            $table->text('significance');
            $table->json('next_paths')->nullable();
            $table->boolean('macro_shift')->default(false);
            $table->timestamps();

            $table->foreign('age_id')->references('id')->on('ages')->cascadeOnDelete();
            $table->foreign('person_id')->references('id')->on('people')->nullOnDelete();
        });

        Schema::create('connections', function (Blueprint $table) {
            $table->id();
            $table->string('source_experiment_id');
            $table->string('target_experiment_id');
            $table->string('type'); // confirms, refutes, builds_on, peer, inspires, anticipates
            $table->text('description');
            $table->timestamps();

            $table->foreign('source_experiment_id')->references('id')->on('experiments')->cascadeOnDelete();
            $table->foreign('target_experiment_id')->references('id')->on('experiments')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('connections');
        Schema::dropIfExists('experiments');
        Schema::dropIfExists('people');
        Schema::dropIfExists('ages');
    }
};
