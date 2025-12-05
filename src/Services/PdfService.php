<?php

/**
 * PDF Generation Service using DomPDF
 * Global Harmony Initiative Website
 */

namespace GHI\Services;

use Dompdf\Dompdf;
use Dompdf\Options;

class PdfService
{
    private readonly Dompdf $dompdf;

    public function __construct()
    {
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'DejaVu Sans');
        $options->set('isPhpEnabled', false); // Security: disable PHP execution

        $this->dompdf = new Dompdf($options);
    }

    /**
     * Generate PDF from HTML string
     *
     * @param string $html HTML content
     * @param string $filename Output filename
     * @param bool $download Whether to download or return as string
     * @return string|null PDF content or download
     */
    public function generateFromHTML(string $html, string $filename = 'document.pdf', bool $download = true)
    {
        $this->dompdf->loadHtml($html);
        $this->dompdf->setPaper('A4', 'portrait');
        $this->dompdf->render();

        if ($download) {
            $this->dompdf->stream($filename, ['Attachment' => 1]);
        } else {
            return $this->dompdf->output();
        }

        return null;
    }

    /**
     * Generate PDF from view template
     *
     * @param string $templatePath Path to template file
     * @param array $data Data to pass to template
     * @param string $filename Output filename
     * @param bool $download Whether to download or return as string
     * @return string|void PDF content or download
     */
    public function generateFromTemplate(string $templatePath, array $data = [], string $filename = 'document.pdf', bool $download = true)
    {
        if (!file_exists($templatePath)) {
            throw new \Exception('Template not found: ' . $templatePath);
        }

        ob_start();
        extract($data);
        include $templatePath;
        $html = ob_get_clean();

        return $this->generateFromHTML($html, $filename, $download);
    }

    /**
     * Generate PDF and save to file
     *
     * @param string $html HTML content
     * @param string $filePath Full file path to save
     * @return bool Success status
     */
    public function saveToFile(string $html, string $filePath): bool
    {
        try {
            $this->dompdf->loadHtml($html);
            $this->dompdf->setPaper('A4', 'portrait');
            $this->dompdf->render();

            $output = $this->dompdf->output();

            // Ensure directory exists
            $dir = dirname($filePath);
            if (!is_dir($dir)) {
                FileService::createDirectory(str_replace(BASE_PATH . '/', '', $dir));
            }

            return file_put_contents($filePath, $output) !== false;
        } catch (\Exception $exception) {
            if (function_exists('log_message')) {
                log_message('error', 'PDF save failed', [
                'path' => $filePath,
                'error' => $exception->getMessage()
                ]);
            }

            return false;
        }
    }
}
