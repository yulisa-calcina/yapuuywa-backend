<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /* ── USUARIOS (extiende la tabla de Laravel) ── */
        Schema::table('users', function (Blueprint $table) {
            $table->string('dni', 8)->unique()->after('id');
            $table->string('nombre', 120)->after('dni');
            $table->string('telefono', 15)->nullable()->after('nombre');
            $table->string('comunidad', 100)->nullable()->after('telefono');
            $table->enum('rol', ['admin', 'ganadero', 'veterinario'])->default('ganadero')->after('comunidad');
            $table->boolean('activo')->default(true)->after('rol');
        });

        /* ── ANIMALES — RF03, RF14 ── */
        Schema::create('animales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('arete', 20);
            $table->string('nombre', 100);
            $table->enum('especie', ['bovino','ovino','porcino','alpaca','camélido','caprino','equino']);
            $table->string('raza', 80)->nullable();
            $table->enum('sexo', ['macho','hembra'])->nullable();
            $table->date('fecha_nac')->nullable();
            $table->decimal('peso_kg', 8, 2)->nullable();
            $table->string('color', 60)->nullable();
            $table->enum('origen', ['nacido','comprado'])->default('nacido');
            $table->decimal('precio_adquisicion', 10, 2)->nullable();
            $table->enum('estado', ['activo','vendido','muerto'])->default('activo');
            $table->text('observaciones')->nullable();
            $table->timestamps();
            $table->unique(['user_id','arete']);
        });

        /* ── HISTORIAL MÉDICO — RF04 ── */
        Schema::create('historial_medicos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('animal_id')->constrained('animales')->cascadeOnDelete();
            $table->date('fecha');
            $table->enum('tipo', ['Vacunación','Desparasitación','Tratamiento médico','Revisión de rutina','Diagnóstico']);
            $table->text('descripcion')->nullable();
            $table->string('medicamento', 120)->nullable();
            $table->string('dosis', 60)->nullable();
            $table->string('veterinario', 100)->nullable();
            $table->decimal('costo', 10, 2)->nullable();
            $table->date('proxima_revision')->nullable();
            $table->boolean('alerta_atendida')->default(false);
            $table->timestamps();
            /* Previene duplicados por animal+fecha+tipo (RNF10) */
            $table->unique(['animal_id','fecha','tipo']);
        });

        /* ── ALERTAS — RF05, RF09 ── */
        Schema::create('alertas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('historial_id')->nullable()->constrained('historial_medicos')->nullOnDelete();
            $table->enum('tipo', ['vacuna','stock'])->default('vacuna');
            $table->enum('estado', ['pendiente','critico','atendido'])->default('pendiente');
            $table->string('descripcion', 255)->nullable();
            $table->string('medicamento', 120)->nullable();
            $table->integer('dias_restantes')->nullable();
            $table->timestamps();
        });

        /* ── PARCELAS — RF06 ── */
        Schema::create('parcelas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('codigo', 20)->unique();
            $table->string('nombre', 100);
            $table->string('ubicacion', 200)->nullable();
            $table->decimal('superficie_ha', 8, 2)->nullable();
            $table->enum('tipo_suelo', ['arcilloso','arenoso','franco','limoso','otro'])->nullable();
            $table->enum('riego', ['lluvia','canal','aspersion','goteo','otro'])->nullable();
            $table->enum('estado', ['activo','descanso','preparacion'])->default('activo');
            $table->timestamps();
        });

        /* ── CICLOS DE CULTIVO — RF07 ── */
        Schema::create('ciclos_cultivo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parcela_id')->constrained('parcelas')->cascadeOnDelete();
            $table->string('cultivo', 80);
            $table->string('variedad', 80)->nullable();
            $table->date('fecha_siembra');
            $table->date('fecha_cosecha_est')->nullable();
            $table->decimal('semilla_kg', 10, 2)->nullable();
            $table->decimal('superficie_ha', 8, 2)->nullable();
            $table->enum('estado', ['crecimiento','cosechado','perdido'])->default('crecimiento');
            $table->decimal('cosecha_kg', 10, 2)->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });

        /* ── INSUMOS — RF08 ── */
        Schema::create('insumos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('nombre', 100);
            $table->enum('categoria', ['agricola','veterinario','general']);
            $table->string('unidad', 20)->default('unidad');
            $table->decimal('stock_actual', 10, 2)->default(0);
            $table->decimal('stock_minimo', 10, 2)->default(0);
            $table->string('proveedor', 100)->nullable();
            $table->decimal('precio_unitario', 10, 2)->nullable();
            $table->timestamps();
        });

        /* ── PERSONAL — RF10 ── */
        Schema::create('personal', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('nombre', 120);
            $table->string('dni', 8)->unique();
            $table->enum('tipo', ['permanente','jornalero'])->default('jornalero');
            $table->string('cargo', 80)->nullable();
            $table->decimal('salario_diario', 10, 2);         /* DECIMAL, no FLOAT (RNF09) */
            $table->string('telefono', 15)->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        /* ── VENTAS — RF11 ── */
        Schema::create('ventas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('fecha');
            $table->string('producto', 100);
            $table->enum('categoria', ['leche','lana','ganado','cosecha','otro']);
            $table->decimal('cantidad', 12, 2);
            $table->decimal('precio_unitario', 12, 2);        /* DECIMAL (RNF09) */
            $table->decimal('total', 12, 2);
            $table->string('comprador', 120)->nullable();
            $table->enum('modalidad', ['contado','credito'])->default('contado');
            $table->string('comprobante', 40)->nullable();
            $table->timestamps();
        });

        /* ── GASTOS — RF12 ── */
        Schema::create('gastos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('fecha');
            $table->enum('categoria', ['insumos','personal','veterinaria','maquinaria','servicios','otro']);
            $table->string('descripcion', 200);
            $table->decimal('monto', 12, 2);                  /* DECIMAL (RNF09) */
            $table->string('proveedor', 120)->nullable();
            $table->string('comprobante', 40)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gastos');
        Schema::dropIfExists('ventas');
        Schema::dropIfExists('personal');
        Schema::dropIfExists('insumos');
        Schema::dropIfExists('ciclos_cultivo');
        Schema::dropIfExists('parcelas');
        Schema::dropIfExists('alertas');
        Schema::dropIfExists('historial_medicos');
        Schema::dropIfExists('animales');
    }
};
