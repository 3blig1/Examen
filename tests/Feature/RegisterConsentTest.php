<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterConsentTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_requires_privacy_consent(): void
    {
        $response = $this->from('/register')->post('/register', [
            'title' => 'mr',
            'first_name' => 'Jean',
            'last_name' => 'Dupont',
            'email' => 'jean@example.com',
            'phone' => '+33123456789',
            'country' => 'France',
            'date_of_birth' => '1995-01-01',
            'birth_place' => 'Paris',
            'sex' => 'male',
            'birth_country' => 'France',
            'nationality' => 'Française',
            'address' => '1 rue de Paris',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors(['privacy_consent']);
    }
}
