<?php

namespace Database\Factories;

use App\Enums\PurchaseRequestStatus;
use App\Models\PurchaseRequest;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PurchaseRequest>
 */
class PurchaseRequestFactory extends Factory
{

    protected $model = PurchaseRequest::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'description' => $this->faker->sentence(3),
            'notes' => $this->faker->optional()->sentence(),
            'item_code' => strtoupper($this->faker->bothify('PRD-###??')),
            'quantity' => $this->faker->numberBetween(1, 10),
            'unit_price' => $this->faker->optional()->randomFloat(2, 10, 1500),
            'eta_date' => $this->faker->optional()->date(),
            'carrier' => $this->faker->optional()->company(),
            'status' => PurchaseRequestStatus::PENDING,
            'customer' => $this->faker->company(),
            'requester_id' => User::factory(),
            'reference_os' => strtoupper($this->faker->bothify('OS-####')),
            'shop_id' => Shop::factory(),
            'cancellation_reason' => null,
        ];
    }
}
