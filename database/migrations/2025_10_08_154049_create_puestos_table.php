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
            Schema::create('puestos', function (Blueprint $table) {
                $table->id();
                $table->string('denominacion', 800);
                $table->enum('nivelJerarquico', ['GOBERNADOR (A)', 'SECRETARIA (O) DEPARTAMENTAL', 'ASESORA (OR) / DIRECTORA (OR) / DIR. SERV. DPTAL.',  'JEFA (E) DE UNIDAD', 'PROFESIONAL I', 'PROFESIONAL II', 'ADMINISTRATIVO I', 'ADMINISTRATIVO II', 'APOYO ADMINISTRATIVO I', 'APOYO ADMINISTRATIVO II', 'ASISTENTE'])->nullable();
                $table->bigInteger('nivel')->nullable();
                $table->string('item', 45)->nullable()->unique();
                $table->string('manual', 500)->nullable();
                $table->text('perfil')->nullable();
                $table->text('experencia')->nullable();
                $table->decimal('haber', 12, 2)->nullable();
                $table->enum('tipoContrato', ['PERMANENTE','EVENTUAL'])->default('PERMANENTE');
                $table->foreignId('idUnidadOrganizacional')->constrained('unidad_organizacionals');
                $table->boolean('esJefatura')->default(false);
                $table->boolean('esActivo')->default(true);
                $table->boolean('estado')->default(true);
                $table->timestamps();

                $table->index(['nivelJerarquico']);
                $table->index(['tipoContrato']);
                $table->index(['esJefatura']);
                $table->index(['esActivo']);
                $table->index(['idUnidadOrganizacional']);
            });
        }

        /**
         * Reverse the migrations.
         */
        public function down(): void
        {
            Schema::dropIfExists('puestos');
        }
    };
