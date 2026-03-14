<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FileViewController extends Controller
{
    public function showBackendFile(Request $request)
    {
        $userRoles = auth()->user()->roles->pluck('name')->toArray();

        if (in_array('EMPLOYEE', $userRoles)) {
            abort(403);
        }
        // -------- 1. Grab and validate the query param -------------
        $code = $request->query('code');

        if (! $code) {
            // Same behaviour as your CodeIgniter redirect
            return abort(404);
        }

        // -------- 2. Decode the URL we stored earlier ---------------
        $decodedPath = customURIDecode($code);         // e.g. https://example.com/uploads/foo.pdf

        // -------- 3. Convert it into an actual file-system path -----
        //  * Replace the app URL with the public_path()
        //  * Normalise the slashes if we’re on Windows
        $filePath = str_replace(
            url('/'),
            public_path(),      // = FCPATH in CodeIgniter
            $decodedPath
        );

        if (PHP_OS_FAMILY === 'Windows') {
            $filePath = str_replace('/', '\\', $filePath);
        }

        // -------- 4. Return or 404 ---------------------------------
        if (! file_exists($filePath)) {
            return abort(404);
        }

        // Laravel’s helper sets the MIME type automatically
        return response()->file($filePath);
        // If you prefer the manual way:
        // return response()->file(
        //     $filePath,
        //     ['Content-Type' => mime_content_type($filePath)]
        // );
    }

    /**
     * Stream a file back to the browser.
     */
    public function show(Request $request)
    {
       
        // -------- 1. Grab and validate the query param -------------
        $code = $request->query('code');

        if (! $code) {
            // Same behaviour as your CodeIgniter redirect
            return abort(404);
        }

        // -------- 2. Decode the URL we stored earlier ---------------
        $decodedPath = customURIDecode($code);         // e.g. https://example.com/uploads/foo.pdf

        // -------- 3. Convert it into an actual file-system path -----
        //  * Replace the app URL with the public_path()
        //  * Normalise the slashes if we’re on Windows
        $filePath = str_replace(
            url('/'),
            public_path(),      // = FCPATH in CodeIgniter
            $decodedPath
        );

        if (PHP_OS_FAMILY === 'Windows') {
            $filePath = str_replace('/', '\\', $filePath);
        }

        // -------- 4. Return or 404 ---------------------------------
        if (! file_exists($filePath)) {
            return abort(404);
        }

        // Laravel’s helper sets the MIME type automatically
        return response()->file($filePath);
        // If you prefer the manual way:
        // return response()->file(
        //     $filePath,
        //     ['Content-Type' => mime_content_type($filePath)]
        // );
    }
}
