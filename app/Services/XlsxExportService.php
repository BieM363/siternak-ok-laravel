<?php

namespace App\Services;

use Symfony\Component\HttpFoundation\BinaryFileResponse;
use ZipArchive;

/**
 * XlsxExportService
 * Generator file Excel (.xlsx / Office Open XML) native tanpa ketergantungan eksternal berat.
 * Mendukung multi-sheet, header berwarna emerald, auto-width kolom, dan kompatibel penuh dengan Microsoft Excel, LibreOffice, & Google Sheets.
 */
class XlsxExportService
{
    /**
     * Membuat file .xlsx dari satu atau beberapa sheet dan mengembalikan response download.
     *
     * @param string $filename Nama file unduhan (berakhiran .xlsx)
     * @param array<int, array{title: string, headers: array<int, string>, rows: array<int, array<int, mixed>>, subtitle?: string}> $sheets
     */
    public static function download(string $filename, array $sheets): BinaryFileResponse
    {
        if (!str_ends_with(strtolower($filename), '.xlsx')) {
            $filename .= '.xlsx';
        }

        $tmpDir = storage_path('app/exports');
        if (!is_dir($tmpDir)) {
            mkdir($tmpDir, 0775, true);
        }

        $tmpPath = $tmpDir . DIRECTORY_SEPARATOR . uniqid('siternak_', true) . '.xlsx';

        $zip = new ZipArchive();
        if ($zip->open($tmpPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException('Gagal membuat arsip file Excel (.xlsx).');
        }

        $sheetCount = count($sheets);

        $zip->addFromString('[Content_Types].xml', self::buildContentTypesXml($sheetCount));
        $zip->addFromString('_rels/.rels', self::buildRootRelsXml());
        $zip->addFromString('xl/workbook.xml', self::buildWorkbookXml($sheets));
        $zip->addFromString('xl/_rels/workbook.xml.rels', self::buildWorkbookRelsXml($sheetCount));
        $zip->addFromString('xl/styles.xml', self::buildStylesXml());

        foreach ($sheets as $index => $sheet) {
            $sheetNum = $index + 1;
            $zip->addFromString(
                "xl/worksheets/sheet{$sheetNum}.xml",
                self::buildWorksheetXml(
                    $sheet['headers'] ?? [],
                    $sheet['rows'] ?? [],
                    $sheet['subtitle'] ?? null
                )
            );
        }

        $zip->close();

        return response()
            ->download($tmpPath, $filename, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ])
            ->deleteFileAfterSend(true);
    }

    private static function xmlEscape(mixed $value): string
    {
        $str = (string) ($value ?? '');
        $str = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $str) ?? '';

        return htmlspecialchars($str, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }

    private static function columnLetter(int $index): string
    {
        $letter = '';
        $num = $index + 1;
        while ($num > 0) {
            $mod = ($num - 1) % 26;
            $letter = chr(65 + $mod) . $letter;
            $num = intdiv($num - 1, 26);
        }

        return $letter;
    }

    private static function sanitizeSheetName(string $name, int $index): string
    {
        $clean = preg_replace('/[\\\\\/\?\*\[\]\:]/', ' ', trim($name)) ?: "Sheet{$index}";
        $clean = mb_substr(preg_replace('/\s+/', ' ', $clean), 0, 31);

        return $clean !== '' ? $clean : "Sheet{$index}";
    }

    private static function buildContentTypesXml(int $sheetCount): string
    {
        $overrides = '';
        for ($i = 1; $i <= $sheetCount; $i++) {
            $overrides .= '<Override PartName="/xl/worksheets/sheet' . $i . '.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>';
        }

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            . '<Default Extension="xml" ContentType="application/xml"/>'
            . '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
            . '<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>'
            . $overrides
            . '</Types>';
    }

    private static function buildRootRelsXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
            . '</Relationships>';
    }

    private static function buildWorkbookXml(array $sheets): string
    {
        $sheetTags = '';
        foreach ($sheets as $idx => $sheet) {
            $num = $idx + 1;
            $name = self::xmlEscape(self::sanitizeSheetName($sheet['title'] ?? "Sheet{$num}", $num));
            $sheetTags .= '<sheet name="' . $name . '" sheetId="' . $num . '" r:id="rId' . $num . '"/>';
        }

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
            . '<sheets>' . $sheetTags . '</sheets>'
            . '</workbook>';
    }

    private static function buildWorkbookRelsXml(int $sheetCount): string
    {
        $rels = '';
        for ($i = 1; $i <= $sheetCount; $i++) {
            $rels .= '<Relationship Id="rId' . $i . '" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet' . $i . '.xml"/>';
        }
        $styleRid = $sheetCount + 1;
        $rels .= '<Relationship Id="rId' . $styleRid . '" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>';

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . $rels
            . '</Relationships>';
    }

    private static function buildStylesXml(): string
    {
        // Style 0: default, Style 1: title/banner bold, Style 2: header emerald fill + white bold, Style 3: data cell with border
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            . '<fonts count="3">'
            . '<font><sz val="10"/><name val="Calibri"/></font>'
            . '<font><b/><sz val="11"/><color rgb="FF064E3B"/><name val="Calibri"/></font>'
            . '<font><b/><sz val="10"/><color rgb="FFFFFFFF"/><name val="Calibri"/></font>'
            . '</fonts>'
            . '<fills count="4">'
            . '<fill><patternFill patternType="none"/></fill>'
            . '<fill><patternFill patternType="gray125"/></fill>'
            . '<fill><patternFill patternType="solid"><fgColor rgb="FF047857"/><bgColor indexed="64"/></patternFill></fill>'
            . '<fill><patternFill patternType="solid"><fgColor rgb="FFECFDF5"/><bgColor indexed="64"/></patternFill></fill>'
            . '</fills>'
            . '<borders count="2">'
            . '<border><left/><right/><top/><bottom/><diagonal/></border>'
            . '<border>'
            . '<left style="thin"><color rgb="FFCBD5E1"/></left>'
            . '<right style="thin"><color rgb="FFCBD5E1"/></right>'
            . '<top style="thin"><color rgb="FFCBD5E1"/></top>'
            . '<bottom style="thin"><color rgb="FFCBD5E1"/></bottom>'
            . '<diagonal/>'
            . '</border>'
            . '</borders>'
            . '<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>'
            . '<cellXfs count="4">'
            . '<xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>'
            . '<xf numFmtId="0" fontId="1" fillId="3" borderId="0" xfId="0" applyFont="1" applyFill="1"/>'
            . '<xf numFmtId="0" fontId="2" fillId="2" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="left" vertical="center" wrapText="1"/></xf>'
            . '<xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0" applyBorder="1" applyAlignment="1"><alignment vertical="center"/></xf>'
            . '</cellXfs>'
            . '</styleSheet>';
    }

    private static function buildWorksheetXml(array $headers, array $rows, ?string $subtitle = null): string
    {
        $colCount = max(1, count($headers));
        $widths = array_fill(0, $colCount, 12);

        foreach ($headers as $cIdx => $h) {
            $widths[$cIdx] = max($widths[$cIdx], min(42, mb_strlen((string) $h) + 5));
        }

        foreach ($rows as $row) {
            foreach (array_values($row) as $cIdx => $val) {
                if ($cIdx < $colCount) {
                    $widths[$cIdx] = max($widths[$cIdx], min(45, mb_strlen((string) ($val ?? '')) + 4));
                }
            }
        }

        $colsXml = '<cols>';
        foreach ($widths as $cIdx => $w) {
            $num = $cIdx + 1;
            $colsXml .= '<col min="' . $num . '" max="' . $num . '" width="' . $w . '" customWidth="1"/>';
        }
        $colsXml .= '</cols>';

        $sheetData = '<sheetData>';
        $rowNum = 1;

        if ($subtitle) {
            $sheetData .= '<row r="' . $rowNum . '" ht="22" customHeight="1">'
                . '<c r="A' . $rowNum . '" t="inlineStr" s="1"><is><t>' . self::xmlEscape($subtitle) . '</t></is></c>'
                . '</row>';
            $rowNum += 2;
        }

        // Header row
        $sheetData .= '<row r="' . $rowNum . '" ht="24" customHeight="1">';
        foreach (array_values($headers) as $cIdx => $header) {
            $ref = self::columnLetter($cIdx) . $rowNum;
            $sheetData .= '<c r="' . $ref . '" t="inlineStr" s="2"><is><t>' . self::xmlEscape($header) . '</t></is></c>';
        }
        $sheetData .= '</row>';
        $rowNum++;

        // Data rows
        foreach ($rows as $row) {
            $sheetData .= '<row r="' . $rowNum . '">';
            foreach (array_values($row) as $cIdx => $val) {
                $ref = self::columnLetter($cIdx) . $rowNum;
                if (is_int($val) || is_float($val)) {
                    $sheetData .= '<c r="' . $ref . '" s="3"><v>' . $val . '</v></c>';
                } else {
                    $sheetData .= '<c r="' . $ref . '" t="inlineStr" s="3"><is><t>' . self::xmlEscape($val) . '</t></is></c>';
                }
            }
            $sheetData .= '</row>';
            $rowNum++;
        }

        $sheetData .= '</sheetData>';

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            . $colsXml
            . $sheetData
            . '</worksheet>';
    }
}
