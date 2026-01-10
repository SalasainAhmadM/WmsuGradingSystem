<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\FromArray;

class ExportTemplate implements FromCollection, WithHeadings, WithTitle, WithEvents, WithStyles, ShouldAutoSize 
{
    protected $data;
    protected $title;
    protected $header;

    public function __construct(array $data = [], $header = [], $title = "", array $comments = []){
        $this->data = $data;
        $this->header = $header;
        $this->title = $title;
        $this->comments = $comments;
    }

    public function collection(){
        return collect($this->data)->map(fn($row, $index) => array_merge( (array) $row));
        // return collect($this->data)->map(fn($row, $index) => array_merge([$index + 1], $row));
    }


    public function headings(): array{
        return array_merge($this->header);
    }

    public function title(): string{
        return $this->title;
    }

    public function registerEvents(): array{
        return [
            AfterSheet::class => function(AfterSheet $event) {
                // Freeze the first row (row 1)
                $event->sheet->getDelegate()->freezePane('A2');
                foreach ($this->comments as $cell => $comment) {
                    $cell = $this->columnIndexToExcelColumn($comment['column']).$comment['row']+1;
                    $sheet = $event->sheet->getDelegate();
                    $cellcomment = $sheet->getComment($cell); // e.g., 'B2'
                    $cellcomment->getText()->createTextRun($comment['error_message']);
                    $cellcomment->setAuthor('System');
                    $sheet->getStyle($cell)->applyFromArray([
                        'font' => [
                            'color' => ['rgb' => 'FF0000'], // White
                        ],
                    ]);
                }
            },
        ];
    }
    public function columnIndexToExcelColumn($index) {
        $column = '';
        while ($index >= 0) {
            $column = chr($index % 26 + 65) . $column;
            $index = intdiv($index, 26) - 1;
        }
        return $column;
    }
    
    public function styles(Worksheet $sheet){
        return [
            1 => ['font' => ['bold' => true]], // Make header row bold
        ];
    }
}
