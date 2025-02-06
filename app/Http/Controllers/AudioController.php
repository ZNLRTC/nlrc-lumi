<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AudioController extends Controller
{
    public function getAudio($filename) {

        if (!Storage::disk('media')->exists($filename)) {
            return response()->json(['error' => 'File not found'], 404);
        }

        $audioFile = Storage::disk('media')->url($filename);

        return redirect($audioFile);
    }
}
