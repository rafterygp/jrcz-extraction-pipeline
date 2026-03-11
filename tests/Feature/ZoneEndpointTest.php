<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ZoneEndpointTest extends TestCase
{
    public function test_it_returns_valid_geojson_for_existing_zone(): void
    {
        // 1. Stage a fake file in the test storage so the controller doesn't complain
        Storage::fake('local'); // Change 'local' if your app uses a different disk like 'public'
        Storage::disk('local')->put('test_data.gpkg', 'dummy binary content');

        // 2. Add the missing 'file' parameter to your endpoint query
        $response = $this->getJson('/fetch-geojson?type=gemeente&name=Rotterdam&file=test_data.gpkg');

        // Optional: Keep dump() here temporarily if you want to see the new response
        // $response->dump(); 

        $response->assertStatus(200);
        
        $response->assertJsonStructure([
            'type',
            'geometry' => [
                'type',
                'coordinates'
            ],
            'properties' => [
                'type',
                'name'
            ]
        ]);
    }

    public function test_it_handles_not_found_zones_gracefully(): void
    {
        Storage::fake('local');
        Storage::disk('local')->put('test_data.gpkg', 'dummy binary content');

        // Querying a non-existent zone like 'Atlantis'
        $response = $this->getJson('/fetch-geojson?type=gemeente&name=Atlantis&file=test_data.gpkg');

        // Asserts your app gracefully returns a 404 (or 400) instead of crashing
        $response->assertStatus(404); 
    }
}