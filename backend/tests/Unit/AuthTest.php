<?php

namespace Tests\Unit;

// use PHPUnit\Framework\TestCase;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthTest extends TestCase
{

    use RefreshDatabase;

    private $access_token = "";

    /**
     * A basic unit test example.
     */
    public function test_that_register_user_works(): void
    {
        $response = $this->withHeaders([
            //
        ])->post('/api/register', [
                    "first_name" => "John",
                    "last_name" => "Test",
                    "username" => "JohnTest1",
                    "email" => "john.test1@example.com",
                    "password" => "password123",
                    "password_confirmation" => "password123",
                ]);

        $response->assertStatus(201);
        // $this->access_token = $response->json();
    }

    public function test_that_login_user_works(): void
    {

        $response = $this->withHeaders([
            //
        ])->post('/api/register', [
                    "first_name" => "John",
                    "last_name" => "Test",
                    "username" => "JohnTest1",
                    "email" => "john.test1@example.com",
                    "password" => "password123",
                    "password_confirmation" => "password123",
                ]);

        // $response->assertStatus(201);

        $response = $this->withHeaders([
            //
        ])->post('/api/login', [
                    "email" => "john.test1@example.com",
                    "password" => "password123",
                ]);

        $response->assertStatus(200);
    }

    public function test_that_refresh_user_works(): void
    {

        $response = $this->post('/api/register', [
            "first_name" => "John",
            "last_name" => "Test",
            "username" => "JohnTest1",
            "email" => "john.test1@example.com",
            "password" => "password123",
            "password_confirmation" => "password123",
        ]);

        $response = $this->post('/api/login', [
            "email" => "john.test1@example.com",
            "password" => "password123",
        ]);


        $response = $this->withCookie("refresh_token", $response->getCookie("refresh_token")->getValue())->post('/api/refresh');

        $response->assertStatus(200);


    }
}
