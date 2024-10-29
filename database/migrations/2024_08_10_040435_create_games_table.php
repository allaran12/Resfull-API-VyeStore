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
        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('surname');
            $table->decimal('price', 8, 2);
            $table->text('desc');
            $table->string('link');
            $table->string('release');
            $table->string('platforms');
            $table->string('genre');
            $table->string('developers');
            $table->string('publishers');
            $table->boolean('inCart')->default(false);
            $table->boolean('selected')->default(false);
            $table->boolean('isHovered')->default(false);
            $table->boolean('isLiked')->default(false);
            $table->integer('rating');
            $table->string('cover');
            $table->json('footage');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('games');
    }
};
