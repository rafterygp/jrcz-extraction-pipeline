<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

class FetchGeojsonTest extends TestCase
{
    public function test_it_serves_structured_geojson_data(): void
    {
        $dir = public_path('geopackages');
        if (!File::exists($dir)) File::makeDirectory($dir, 0755, true);

        $mockData = json_encode([
            'type' => 'FeatureCollection',
            'features' => [[
                'type' => 'Feature',
                'properties' => ['statnaam' => 'Groningen', 'rubriek' => 'provincie'],
                'geometry' => ['type' => 'MultiPolygon', 'coordinates' => []]
            ]]
        ]);
        
        File::put($dir . '/test.geojson', $mockData);

        $response = $this->getJson(route('fetch.geojson', ['file' => 'test.geojson']));

        $response->assertStatus(200)
                 ->assertJsonStructure(['geoJsonData' => ['type', 'features']])
                 ->assertJsonPath('geoJsonData.features.0.properties.statnaam', 'Groningen');

        File::delete($dir . '/test.geojson');
    }
}