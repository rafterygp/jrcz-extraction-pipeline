<?php

namespace Tests\Feature;

use Tests\TestCase;

class HomeViewTest extends TestCase
{
    public function test_map_dashboard_renders_successfully(): void
    {
        $response = $this->get(route('mains.index')); 
        $response->assertStatus(200);
        $response->assertViewIs('mains.index'); 
    }
}