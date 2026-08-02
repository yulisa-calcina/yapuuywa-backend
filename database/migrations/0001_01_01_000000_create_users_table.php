<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('genero', ['masculino','femenino','otro'])->nullable()->after('nombre');
            $table->string('foto_url', 255)->nullable()->after('genero');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['genero', 'foto_url']);
        });
    }
};