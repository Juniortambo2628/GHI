<?php

/**
 * CSV Handling Service using League CSV
 * Global Harmony Initiative Website
 */

namespace GHI\Services;

use League\Csv\Reader;
use League\Csv\Writer;
use League\Csv\Statement;
use League\Csv\CannotInsertRecord;

class CsvService
{
    /**
     * Read CSV file
     *
     * @param string $filePath Path to CSV file
     * @param bool $hasHeader Whether CSV has header row
     * @return array Array of rows (associative if hasHeader is true)
     */
    public function read(string $filePath, bool $hasHeader = true): array
    {
        try {
            if (!file_exists($filePath)) {
                throw new \Exception('CSV file not found: ' . $filePath);
            }

            $csv = Reader::createFromPath($filePath, 'r');

            if ($hasHeader) {
                $csv->setHeaderOffset(0);
                $records = Statement::create()->process($csv);
                return iterator_to_array($records);
            }

            return iterator_to_array($csv->getRecords());
        } catch (\Exception $exception) {
            if (function_exists('log_message')) {
                log_message('error', 'CSV read failed', [
                    'path' => $filePath,
                    'error' => $exception->getMessage()
                ]);
            }

            return [];
        }
    }

    /**
     * Write data to CSV file
     *
     * @param array $data Array of associative arrays
     * @param string $filePath Output file path
     * @return bool Success status
     */
    public function write(array $data, string $filePath): bool
    {
        try {
            // Ensure directory exists
            $dir = dirname($filePath);
            if (!is_dir($dir)) {
                FileService::createDirectory(str_replace(BASE_PATH . '/', '', $dir));
            }

            $csv = Writer::createFromPath($filePath, 'w+');

            if ($data !== []) {
                // Write header
                $csv->insertOne(array_keys($data[0]));

                // Write data
                $csv->insertAll($data);
            }

            return true;
        } catch (CannotInsertRecord $cannotInsertRecord) {
            if (function_exists('log_message')) {
                log_message('error', 'CSV write failed', [
                    'path' => $filePath,
                    'error' => $cannotInsertRecord->getMessage()
                ]);
            }

            return false;
        }
    }

    /**
     * Download CSV file
     *
     * @param array $data Array of associative arrays
     * @param string $filename Output filename
     */
    public function download(array $data, string $filename = 'export.csv'): void
    {
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $csv = Writer::createFromStream(fopen('php://output', 'w'));

        if ($data !== []) {
            $csv->insertOne(array_keys($data[0]));
            $csv->insertAll($data);
        }
    }

    /**
     * Convert array to CSV string
     *
     * @param array $data Array of associative arrays
     * @return string CSV content
     */
    public function toString(array $data): string
    {
        try {
            $csv = Writer::createFromString();

            if ($data !== []) {
                $csv->insertOne(array_keys($data[0]));
                $csv->insertAll($data);
            }

            return $csv->toString();
        } catch (\Exception $exception) {
            if (function_exists('log_message')) {
                log_message('error', 'CSV conversion failed', [
                'error' => $exception->getMessage()
                ]);
            }

            return '';
        }
    }
}
