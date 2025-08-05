<?php

namespace App\Http\Controllers\General;

use App\Http\Controllers\Controller;
use App\Models\TempUpload;
use Illuminate\Http\Request;

class TempUploadController extends Controller
{
    public function uploadImage(Request $request)
    {
        $request->validate([
            'file' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $tempModel = TempUpload::create(['type' => 'image']);

        $media = $tempModel->addMediaFromRequest('file')
            ->usingName('temp_' . time())
            ->toMediaCollection('temp_images');

        return response()->json([
            'message' => 'Uploaded Successfully',
            'data' => [
                'id' => $media->id,
                'url' => $media->getFullUrl()
            ],
        ], 201);
    }
}
