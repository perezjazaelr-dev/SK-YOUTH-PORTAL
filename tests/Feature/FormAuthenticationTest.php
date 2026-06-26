<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FormAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login_when_accessing_forms(): void
    {
        $routes = [
            '/forms/health-consultation',
            '/forms/mental-health',
            '/forms/pabili-medicine',
            '/forms/silid-karunungan',
            '/forms/sports-registration',
        ];

        foreach ($routes as $route) {
            $response = $this->get($route);
            $response->assertRedirect('/login');
        }
    }

    public function test_authenticated_users_can_access_forms(): void
    {
        $user = User::factory()->create();

        $routes = [
            '/forms/health-consultation' => 'health',
            '/forms/mental-health' => 'mental-health',
            '/forms/pabili-medicine' => 'medicine',
            '/forms/silid-karunungan' => 'silid',
            '/forms/sports-registration' => 'sports',
        ];

        foreach ($routes as $route => $formName) {
            $response = $this->actingAs($user)->get($route);
            $response->assertRedirect(route('landing', ['form' => $formName]));
        }
    }
}
