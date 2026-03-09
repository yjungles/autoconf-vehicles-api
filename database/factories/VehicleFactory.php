<?php

namespace Database\Factories;

use App\Enums\CambioEnum;
use App\Enums\CombustivelEnum;
use App\Models\Vehicle;
use App\Models\User;
use App\Models\VehicleImage;
use Illuminate\Database\Eloquent\Factories\Factory;

class VehicleFactory extends Factory
{
    protected $model = Vehicle::class;

    public function definition(): array
    {
        return [
            'placa' => $this->faker->regexify('^[A-Z]{3}[0-9][A-Z0-9][0-9]{2}$'),
            'chassi' => $this->faker->regexify('[A-HJ-NPR-Z0-9]{17}'),
            'marca' => $this->faker->city(),
            'modelo' => $this->faker->lastName(),
            'versao' => $this->generateVehicleVersion(),
            'valor_venda' => $this->faker->randomFloat(2, 0.01, 300000),
            'cor' => $this->faker->randomElement([
                'Preto',
                'Branco',
                'Prata',
                'Cinza',
                'Vermelho',
                'Azul',
                'Verde',
            ]),
            'km' => $this->faker->numberBetween(0, 500000),
            'cambio' => $this->faker->randomElement(CambioEnum::cases())->value,
            'combustivel' => $this->faker->randomElement(CombustivelEnum::cases())->value,
            'user_id' => User::factory(),
        ];
    }

    public function withImages($images = 3): static
    {
        return $this->has(
            VehicleImage::factory()
                ->count($images)
                ->sequence(
                    ['is_cover' => true],
                    ...array_fill(0, $images - 1, ['is_cover' => false])
                ),
            'images'
        );
    }

    private function generateVehicleVersion(): string
    {
        $trims = ['Base','Sport','LX','EX','LT','LTZ','Premier','Touring','Limited'];
        $engines = ['1.0','1.3','1.6','1.8','2.0'];
        $transmissions = ['MT','AT','CVT'];

        return $this->faker->randomElement($engines)
            .' '.$this->faker->randomElement($trims)
            .' '.$this->faker->randomElement($transmissions);
    }
}
