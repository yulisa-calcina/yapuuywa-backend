<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('producciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('animal_id')->nullable()->constrained('animales')->onDelete('set null');
            $table->date('fecha');
            $table->enum('tipo', ['leche','lana','huevo','carne','otro']);
            $table->decimal('cantidad', 10, 2);
            $table->string('unidad', 20)->default('kg');
            $table->decimal('precio_unitario', 10, 2)->nullable();
            $table->decimal('total', 10, 2)->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('producciones');
    }
};