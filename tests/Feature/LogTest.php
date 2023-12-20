<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LogTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_log_view(): void
    {
        $response = $this->get('/event/logs');
        $response->assertStatus(200);
    }
}
