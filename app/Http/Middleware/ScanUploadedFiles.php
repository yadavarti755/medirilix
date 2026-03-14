<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ScanUploadedFiles
{
    // Disallowed content patterns (for code injection, XSS, RCE, etc.)
    protected $suspiciousPatterns = [
        '/<\?php/i',
        '/<script\b[^>]*>(.*?)<\/script>/is',
        '/<\s*foreignObject/i',
        '/<!DOCTYPE\s+svg/i',
        '/import\s+java/i',
        '/import\s+os/i',
        '/exec\s*\(/i',
        '/system\s*\(/i',
        '/base64_decode\s*\(/i',
        '/eval\s*\(/i',
        '/\bfunction\s+[a-zA-Z0-9_]+\s*\(/i',
        '/\bclass\s+[a-zA-Z0-9_]+\b/i',
    ];

    // Allowed extensions
    protected $allowedExtensions = [
        'jpg',
        'jpeg',
        'png',
        'gif',
        'webp',
        'svg',
        'pdf',
        'doc',
        'docx',
        'xls',
        'xlsx',
        'txt',
        'csv',
        'mp4',
    ];

    // Allowed MIME types
    protected $allowedMimeTypes = [
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/webp',
        'image/svg+xml',
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'text/plain',
        'text/csv',
        'video/mp4',
    ];

    public function handle(Request $request, Closure $next)
    {
        $allFiles = $request->allFiles();

        if (!empty($allFiles)) {
            foreach ($this->flattenFiles($allFiles) as $file) {
                if ($this->isSuspiciousFile($file)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Malicious or disallowed file detected.',
                    ], 400);
                }
            }
        }

        return $next($request);
    }

    // Recursively flatten nested files array
    protected function flattenFiles(array $files): array
    {
        $flattened = [];

        foreach ($files as $file) {
            if (is_array($file)) {
                $flattened = array_merge($flattened, $this->flattenFiles($file));
            } elseif ($file instanceof UploadedFile) {
                $flattened[] = $file;
            }
        }

        return $flattened;
    }

    // Core file check logic
    protected function isSuspiciousFile(UploadedFile $file): bool
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $filename = $file->getClientOriginalName();
        $declaredMime = $file->getClientMimeType();

        // 1. Reject if filename has more than one dot
        if (substr_count($filename, '.') > 1) {
            return true;
        }

        // 2. Check extension
        if (!in_array($extension, $this->allowedExtensions)) {
            return true;
        }

        // 3. Check real MIME type using finfo
        $realMime = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $file->getRealPath());
        if (!in_array($realMime, $this->allowedMimeTypes)) {
            return true;
        }

        // 4. Scan contents for malicious code (if under 50MB)
        if ($file->getSize() < 50 * 1024 * 1024) {
            $contents = file_get_contents($file->getRealPath());

            foreach ($this->suspiciousPatterns as $pattern) {
                if (preg_match($pattern, $contents)) {
                    return true;
                }
            }
        }

        return false;
    }
}
