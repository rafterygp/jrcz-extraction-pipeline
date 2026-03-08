<?php

namespace Tests\Feature;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class UploadManagerTest extends TestCase
{
    public function test_geojson_file_is_stored_in_public_directory(): void
    {
        $dir = public_path('geopackages');
        $file = UploadedFile::fake()->create('test_map.geojson', 500);

        $response = $this->post(route('upload.post'), ['file' => $file]);

        $response->assertStatus(302); // Controller returns redirect()->back()
        $this->assertTrue(File::exists($dir . '/test_map.geojson'));

        // Cleanup
        File::delete($dir . '/test_map.geojson');
    }
}