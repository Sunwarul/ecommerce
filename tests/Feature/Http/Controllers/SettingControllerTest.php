<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Setting;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\SettingController
 */
final class SettingControllerTest extends TestCase
{
    use AdditionalAssertions, WithFaker;

    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\SettingController::class,
            'store',
            \App\Http\Requests\SettingStoreRequest::class
        );
    }

    #[Test]
    public function store_behaves_as_expected(): void
    {
        $name = fake()->name();
        $is_active = fake()->boolean();

        $response = $this->post(route('settings.store'), [
            'name' => $name,
            'is_active' => $is_active,
        ]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\SettingController::class,
            'update',
            \App\Http\Requests\SettingUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $setting = Setting::factory()->create();
        $name = fake()->name();
        $is_active = fake()->boolean();

        $response = $this->put(route('settings.update', $setting), [
            'name' => $name,
            'is_active' => $is_active,
        ]);
    }
}
