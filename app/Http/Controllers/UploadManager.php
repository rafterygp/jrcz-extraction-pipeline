<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class UploadManager extends Controller
{
    function upload()
    {
        return view("mains.index");
    }

    public function uploadPost(Request $request)
    {
        $destinationPath = "geopackages";
        $files = glob($destinationPath . '/*');
        foreach ($files as $file) {
            if (is_file($file))
                unlink($file);
        }
        $file = $request->file("file");

        $FileName = $file->getClientOriginalName();

        if ($file->move($destinationPath, $FileName)) {
            return redirect()->back();
        } else {
            echo "Error uploading file";
        }
    }

    public function display()
    {
        return view('mains.index');
    }

    public function fetch(Request $request)
    {
        // Assuming you pass the filename through a request parameter like 'file'
        $fileName = $request->input('file');

        if (!$fileName) {
            return response()->json(['error' => 'No file specified'], 400);
        }

        $filePath = public_path('geopackages') . DIRECTORY_SEPARATOR . $fileName;

        if (!File::exists($filePath)) {
            return response()->json(['error' => 'File not found'], 404);
        }

        // Read and return the contents of the GeoJSON file
        $geoJsonData = File::get($filePath);

        return response()->json(['geoJsonData' => json_decode($geoJsonData)]);
    }
}
