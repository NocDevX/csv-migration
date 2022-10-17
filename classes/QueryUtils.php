<?php

namespace classes;

class QueryUtils
{
    public function sqlToFile($sql, $filename = 'dump')
    {
        $timestamp = time();
        $folderPath = __DIR__ . '/../output';
        $fileName = "{$filename}_{$timestamp}.sql";

        if (!file_exists($folderPath)) {
            mkdir($folderPath);
            @chmod($folderPath, 0777);
        }

        $file = fopen("{$folderPath}/{$fileName}", 'w');
        fwrite($file, "$sql");
        fclose($file);

        return $fileName;
    }
}
