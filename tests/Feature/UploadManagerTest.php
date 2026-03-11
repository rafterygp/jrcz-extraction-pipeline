<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
// Make sure you have the RefreshDatabase trait if you interact with the DB
// use Illuminate\Foundation\Testing\RefreshDatabase; 

class UploadManagerTest extends TestCase
{
    // use RefreshDatabase; 

    public function test_geojson_file_is_stored_in_public_directory()
    {
        // 1. Boot the fake storage INSIDE the test method
        Storage::fake('public');

        // 2. Create the fake file
        $file = UploadedFile::fake()->create('test_map.geojson', 100, 'application/geo+json');

        // 3. Make the request
        $response = $this->post(route('upload.post'), [
            'file' => $file,
        ]);

        // 4. Assert the redirect
        $response->assertStatus(302);
        $response->assertSessionHasNoErrors(); // Good practice to catch silent validation fails

        // 5. Assert the file was saved to your fake disk
        Storage::disk('public')->assertExists('test_map.geojson');
    }
}