<?php
namespace App\Helpers;

class TextExtract
{
    public static function fromFile(string $path, string $ext): string
    {
        $ext = strtolower($ext);
        if ($ext === 'pdf') return self::fromPDF($path);
        if ($ext === 'docx') return self::fromDOCX($path);
        if ($ext === 'epub') return self::fromEPUB($path);
        if (in_array($ext, ['txt','md'])) return (string)@file_get_contents($path) ?: '';
        return '';
    }

    public static function fromPDF(string $path): string
    {
        $text = '';
        $pdftotext = trim(self::which('pdftotext'));
        if ($pdftotext !== '') {
            $tmpTxt = $path . '.txt';
            @shell_exec('"' . $pdftotext . '" "' . $path . '" "' . $tmpTxt . '" 2>&1');
            if (file_exists($tmpTxt)) { $text = (string)file_get_contents($tmpTxt); @unlink($tmpTxt); }
        }
        // OCR fallback se texto curto
        if (mb_strlen(trim($text)) < 50) {
            $tesseract = trim(self::which('tesseract'));
            $pdftoppm = trim(self::which('pdftoppm'));
            if ($tesseract !== '' && $pdftoppm !== '') {
                $prefix = $path . '_page';
                @shell_exec('"' . $pdftoppm . '" -r 200 "' . $path . '" "' . $prefix . '" 2>&1');
                $pages = glob($prefix . '-*.ppm');
                if (empty($pages)) { $pages = glob($prefix . '-*.png'); }
                $pages = array_slice((array)$pages, 0, 3); // primeiras 3 páginas
                foreach ($pages as $img) {
                    $tmpOut = $img . '.txt';
                    @shell_exec('"' . $tesseract . '" "' . $img . '" "' . $tmpOut . '" -l por+eng 2>&1');
                    if (file_exists($tmpOut . '.txt')) {
                        $text .= "\n" . (string)file_get_contents($tmpOut . '.txt');
                        @unlink($tmpOut . '.txt');
                    }
                    @unlink($img);
                }
            }
        }
        return $text;
    }

    public static function fromDOCX(string $path): string
    {
        $text = '';
        $zip = new \ZipArchive();
        if ($zip->open($path) === true) {
            $xml = $zip->getFromName('word/document.xml');
            if ($xml) {
                $xml = preg_replace('/<w:p[\s\S]*?>/i', "\n", $xml);
                $xml = strip_tags($xml);
                $text = html_entity_decode($xml, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            }
            $zip->close();
        }
        return $text;
    }

    public static function fromEPUB(string $path): string
    {
        $text = '';
        $zip = new \ZipArchive();
        if ($zip->open($path) === true) {
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $name = $zip->getNameIndex($i);
                if (preg_match('/\.(xhtml|html)$/i', $name)) {
                    $html = $zip->getFromIndex($i);
                    if ($html) {
                        $clean = strip_tags($html);
                        $text .= "\n" . html_entity_decode($clean, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                    }
                }
            }
            $zip->close();
        }
        return $text;
    }

    private static function which(string $bin): string
    {
        $cmd = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' ? ('where ' . $bin . ' 2>NUL') : ('which ' . $bin . ' 2>/dev/null');
        $out = shell_exec($cmd);
        return is_string($out) ? trim(explode("\n", $out)[0]) : '';
    }
}
