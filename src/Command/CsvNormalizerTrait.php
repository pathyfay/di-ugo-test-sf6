<?php


namespace App\Command;

use League\Csv\Reader;

trait CsvNormalizerTrait
{
    public function normalizeCsvFile(string $filePath, string $delimiter = ';'): Reader
    {
        $content = file_get_contents($filePath);
        $content = str_replace(',', $delimiter, $content);

        $tempFile = sys_get_temp_dir() . '/temp_' . basename($filePath);
        file_put_contents($tempFile, $content);

        $csvReader = Reader::createFromPath($tempFile, 'r');
        $csvReader->setDelimiter($delimiter);
        $csvReader->setHeaderOffset(0);

        return $csvReader;
    }
}
