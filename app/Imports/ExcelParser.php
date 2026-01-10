<?php

namespace App\Imports;

use PhpOffice\PhpSpreadsheet\IOFactory;

class ExcelParser
{
    protected $sheets = [];

    public function __construct($file)
    {
        $spreadsheet = IOFactory::load($file->getRealPath());
        $sheetCount = $spreadsheet->getSheetCount();

        for ($i = 0; $i < $sheetCount; $i++) {
            $sheet = $spreadsheet->getSheet($i);
            $sheetData = $sheet->toArray();

            $this->sheets[] = [
                'sheet_index' => $i,
                'sheet_title' => $sheet->getTitle(),
                'data' => $sheetData,
            ];
        }
    }

    public function getSheets()
    {
        return $this->sheets;
    }
}

