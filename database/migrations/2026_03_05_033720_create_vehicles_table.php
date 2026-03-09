<?php

use App\Enums\CambioEnum;
use App\Enums\CombustivelEnum;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('placa')->unique();
            $table->string('chassi', 17)->unique();
            $table->string('marca');
            $table->string('modelo');
            $table->string('versao')->nullable();
            $table->decimal('valor_venda', 15, 2);
            $table->string('cor')->nullable();
            $table->unsignedBigInteger('km')->default(0);
            $table->enum('cambio', CambioEnum::cases());
            $table->enum('combustivel', CombustivelEnum::cases());

            $table->foreignIdFor(User::class)->constrained()->onDelete('cascade');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
