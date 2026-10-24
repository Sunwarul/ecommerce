<?php

beforeEach(fn () => config(['app.user_registration_allowed' => true]));

test('registration is off unless enabled', function () {
    config(['app.user_registration_allowed' => false]);

    $this->get('/register')->assertNotFound();
    $this->post('/register', ['name' => 'x'])->assertNotFound();
});

test('registration screen can be rendered when enabled', function () {
    $this->get('/register')->assertStatus(200);
});

test('new users can register when enabled', function () {
    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'Str0ng-Passw0rd!',
        'password_confirmation' => 'Str0ng-Passw0rd!',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));
});

test('self-registered users get no admin access', function () {
    $this->post('/register', [
        'name' => 'Stranger',
        'email' => 'stranger@example.com',
        'password' => 'Str0ng-Passw0rd!',
        'password_confirmation' => 'Str0ng-Passw0rd!',
    ]);

    $this->get(route('users.index'))->assertForbidden();
});
