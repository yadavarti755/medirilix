<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait FileUploadTraits
{
    public function uploadFile($file, $folder)
    {
        if ($file) {
            $originalName = $file->getClientOriginalName();
            $mimeType = $file->getMimeType();
            $size = $file->getSize();
            $fileName = Str::random(20) . '.' . $file->getClientOriginalExtension();
            // Store the file in the specified directory within the 'public' disk
            $filePath = $file->storeAs($folder, $fileName, 'public');
            return [
                'file_name' => $fileName,
                'file_path' => $filePath,
                'original_name' => $originalName,
                'mime_type' => $mimeType,
                'size' => $size
            ];
        }
        return;
    }
}
