<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
// use App\Services\GeojsonExtractor; // Uncomment and link to your actual extraction class

class GeojsonExtractionTest extends TestCase
{
    /**
     * Test that raw polygon coordinates are cleanly transformed into standard GeoJSON format.
     */
    public function test_it_formats_raw_polygon_to_valid_geojson(): void
    {
        // 1. Arrange: Define the raw spatial data (simulating DB output)
        // Using a basic closed loop to represent a simple Buurt/Gemeente polygon
        $rawCoordinates = [
            [4.895168, 52.370216], 
            [4.903561, 52.367984], 
            [4.898123, 52.364219],
            [4.895168, 52.370216] // Loop closure
        ];
        
        $zoneType = 'Gemeente';
        $zoneName = 'Amsterdam';

        // Instantiate your extraction logic (adjust to your actual class name)
        // $extractor = new GeojsonExtractor();

        // 2. Act: Run the data through your pipeline
        // $payload = $extractor->generatePayload($rawCoordinates, $zoneType, $zoneName);

        // --- MOCK PAYLOAD FOR DEMONSTRATION ---
        $payload = [
            'type' => 'Feature',
            'geometry' => [
                'type' => 'Polygon',
                'coordinates' => [$rawCoordinates]
            ],
            'properties' => [
                'type' => $zoneType,
                'name' => $zoneName
            ]
        ];
        // --------------------------------------

        // 3. Assert: Verify the structural integrity of the output
        $this->assertIsArray($payload, 'The payload must be an array before JSON encoding.');
        $this->assertEquals('Feature', $payload['type']);
        $this->assertEquals('Polygon', $payload['geometry']['type']);
        
        // Ensure coordinate vertices are preserved exactly
        $this->assertEquals($rawCoordinates, $payload['geometry']['coordinates'][0]);
        
        // Validate properties injection
        $this->assertEquals('Gemeente', $payload['properties']['type']);
        $this->assertEquals('Amsterdam', $payload['properties']['name']);
    }
}