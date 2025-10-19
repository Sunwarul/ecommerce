<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Warehouse;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\WarehouseController
 */
final class WarehouseControllerTest extends TestCase
{
    use AdditionalAssertions, WithFaker;

    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\WarehouseController::class,
            'store',
            \App\Http\Requests\WarehouseStoreRequest::class
        );
    }

    #[Test]
    public function store_behaves_as_expected(): void
    {
        $name = fake()->name();
        $address = fake()->word();
        $phone = fake()->phoneNumber();

        $response = $this->post(route('warehouses.store'), [
            'name' => $name,
            'address' => $address,
            'phone' => $phone,
        ]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\WarehouseController::class,
            'update',
            \App\Http\Requests\WarehouseUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $warehouse = Warehouse::factory()->create();
        $name = fake()->name();
        $address = fake()->word();
        $phone = fake()->phoneNumber();

        $response = $this->put(route('warehouses.update', $warehouse), [
            'name' => $name,
            'address' => $address,
            'phone' => $phone,
        ]);
    }
}
