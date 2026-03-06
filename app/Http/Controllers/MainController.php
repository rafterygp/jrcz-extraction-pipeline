<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class MainController extends Controller
{
    public function index()
    {
        // Scan the directory for the active database file
        $path = public_path('geopackages');
        $files = File::exists($path) ? File::files($path) : [];
        $activeFile = count($files) > 0 ? $files[0]->getFilename() : null;

        // Pass only the active file state to the view
        return view('mains.index', compact('activeFile'));
    }
}