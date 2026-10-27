<?php

namespace App\Services;

use Mpdf\Mpdf;
use Mpdf\Output\Destination;
use Symfony\Component\HttpFoundation\Response;

class PdfService
{
    /**
     * Menghasilkan response file PDF menggunakan engine mPDF.
     *
     * @param  string  $html  Konten HTML yang akan dirender ke PDF
     * @param  string  $filename  Nama berkas ketika diunduh/dilihat
     * @param  string  $orientation  Orientasi halaman: 'P' (Portrait) atau 'L' (Landscape)
     * @param  string  $format  Ukuran kertas (default: 'A4')
     * @param  bool  $inline  Tampilkan langsung di browser (true) atau force download (false)
     */
    public function render(
        string $html,
        string $filename = 'dokumen.pdf',
        string $orientation = 'P',
        string $format = 'A4',
        bool $inline = true,
    ): Response {
        $tempDir = storage_path('framework/cache/mpdf');
        if (! is_dir($tempDir)) {
            mkdir($tempDir, 0775, true);
        }

        $formatConfig = $orientation === 'L' ? $format.'-L' : $format;

        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => $formatConfig,
            'margin_left' => 12,
            'margin_right' => 12,
            'margin_top' => 14,
            'margin_bottom' => 14,
            'margin_header' => 6,
            'margin_footer' => 6,
            'tempDir' => $tempDir,
            'default_font' => 'dejavusans',
            'autoScriptToLang' => true,
            'autoLangToFont' => true,
        ]);

        $mpdf->SetTitle($filename);
        $mpdf->SetAuthor(config('app.name', 'KOMPLAIN'));
        $mpdf->SetCreator('KOMPLAIN System via mPDF');

        // Footer otomatis dengan penomoran halaman dan waktu cetak
        $footerText = '<table width="100%" style="font-size: 7.5pt; color: #1e293b; border-top: 1px solid #000000; padding-top: 4px;">
            <tr>
                <td style="text-align: left; width: 45%;">Dokumen Resmi Rekapitulasi Sistem KOMPLAIN &bull; Terbatas</td>
                <td style="text-align: center; width: 25%;">Halaman {PAGENO} dari {nbpg}</td>
                <td style="text-align: right; width: 30%;">Dicetak: '.now()->translatedFormat('d F Y, H:i').' WIB</td>
            </tr>
        </table>';
        $mpdf->SetHTMLFooter($footerText);

        $mpdf->WriteHTML($html);

        $binary = $mpdf->Output($filename, Destination::STRING_RETURN);
        $disposition = $inline ? 'inline' : 'attachment';

        return response($binary, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => "{$disposition}; filename=\"{$filename}\"",
            'Cache-Control' => 'private, must-revalidate, post-check=0, pre-check=0, max-age=1',
            'Pragma' => 'public',
            'Expires' => '0',
        ]);
    }
}
