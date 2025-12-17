<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;

trait PdfGenerator
{
    /**
     * Returns a writable temp directory for mPDF and creates it if necessary.
     */
    protected static function getMpdfTempDir(): string
    {
        $tmp = storage_path('app/mpdf/tmp');
        if (! file_exists($tmp)) {
            mkdir($tmp, 0755, true);
        }
        return $tmp;
    }

    /**
     * Generate and prompt-download a PDF.
     */
    public static function generatePdf($view, $filePrefix, $filePostfix, $pdfType = null, $requestFrom = 'admin'): string
    {
        $mpdf = new \Mpdf\Mpdf([
            'default_font'   => 'FreeSerif',
            'mode'           => 'utf-8',
            'format'         => [190, 250],
            'autoLangToFont' => true,
            'tempDir'        => self::getMpdfTempDir(),
        ]);
        $mpdf->autoScriptToLang = true;
        $mpdf->autoLangToFont   = true;

        $html = $view->render();
        $mpdf->WriteHTML($html);

        // 'D' for download
        $mpdf->Output("{$filePrefix}{$filePostfix}.pdf", \Mpdf\Output\Destination::DOWNLOAD);
    }

    /**
     * Generate and save a PDF to disk, returning its path.
     */
    public static function storePdf($view, $filePrefix, $filePostfix, $pdfType = null, $requestFrom = 'admin'): string
    {
        $mpdf = new \Mpdf\Mpdf([
            'default_font'   => 'FreeSerif',
            'mode'           => 'utf-8',
            'format'         => [190, 250],
            'autoLangToFont' => true,
            'tempDir'        => self::getMpdfTempDir(),
        ]);
        $mpdf->autoScriptToLang = true;
        $mpdf->autoLangToFont   = true;

        // Only set footer for invoices
        if ($pdfType === 'invoice') {
            $footerHtml = self::footerHtml($requestFrom);
            $mpdf->SetHTMLFooter($footerHtml);
        }

        $html = $view->render();
        $mpdf->WriteHTML($html);

        // Ensure the 'invoices' directory exists
        $dir = 'invoices';
        if (! Storage::disk('public')->exists($dir)) {
            Storage::disk('public')->makeDirectory($dir);
        }

        $fileName = "{$filePrefix}{$filePostfix}.pdf";
        $filePath = Storage::disk('public')->path("{$dir}/{$fileName}");

        // Save the PDF file
        $mpdf->Output($filePath, \Mpdf\Output\Destination::FILE);

        return $filePath;
    }

    /**
     * Returns custom HTML footer based on theme & request origin.
     */
    public static function footerHtml(string $requestFrom): string
    {
        $getCompanyPhone = getWebConfig(name: 'company_phone');
        $getCompanyEmail = getWebConfig(name: 'company_email');

        if ($requestFrom === 'web' && (theme_root_path() === 'theme_aster' || theme_root_path() === 'theme_fashion')) {
            return '<div style="width:560px;margin: 0 auto;background-color: #1455AC">
                <table class="fz-10">
                    <tr>
                        <td style="padding: 10px">
                            <span style="color:#ffffff;">' . url('/') . '</span>
                        </td>
                        <td style="padding: 10px">
                            <span style="color:#ffffff;">' . $getCompanyPhone . '</span>
                        </td>
                        <td style="padding: 10px">
                            <span style="color:#ffffff;">' . $getCompanyEmail . '</span>
                        </td>
                    </tr>
                </table>
            </div>';
        }

        return '<div style="width:520px;margin: 0 auto;background-color: #F2F4F7;padding: 11px 19px 10px 32px;">
            <table class="fz-10">
                <tr>
                    <td>
                        <span>' . url('/') . '</span>
                    </td>
                    <td>
                        <span>' . $getCompanyPhone . '</span>
                    </td>
                    <td>
                        <span>' . $getCompanyEmail . '</span>
                    </td>
                </tr>
            </table>
        </div>';
    }
}
