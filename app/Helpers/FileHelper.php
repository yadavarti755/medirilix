<?php

namespace App\Helpers;

class FileHelper
{
    public static function formatSize($bytes)
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } elseif ($bytes > 1) {
            return $bytes . ' bytes';
        } elseif ($bytes == 1) {
            return $bytes . ' byte';
        } else {
            return '0 bytes';
        }
    }

    public static function getFileInfo($path)
    {
        if (!file_exists(public_path($path))) {
            return null;
        }

        $fullPath = public_path($path);
        $size = self::formatSize(filesize($fullPath));
        $ext = strtoupper(pathinfo($fullPath, PATHINFO_EXTENSION));

        // Add usage instruction based on type
        $instruction = match ($ext) {
            'PDF' => 'Requires Adobe Acrobat Reader.',
            'DOC', 'DOCX' => 'Requires MS Word or LibreOffice.',
            'XLS', 'XLSX' => 'Requires MS Excel or LibreOffice.',
            'PPT', 'PPTX' => 'Requires MS PowerPoint or LibreOffice.',
            default => 'Standard file format.',
        };

        return [
            'size' => $size,
            'ext' => $ext,
            'instruction' => $instruction,
        ];
    }
}
