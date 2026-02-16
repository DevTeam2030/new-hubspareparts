<?php

namespace App\Exports\ProductImportSheets;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class UnitSheet implements FromArray, WithHeadings, WithTitle, WithEvents
{
    public function array(): array
    {
        return [
            ['kg'],
            ['pc'],
            ['gms'],
            ['ltrs'],
            ['pair'],
            ['oz'],
            ['lb'],
        ];
    }

    public function headings(): array
    {
        return ['Unit'];
    }

    public function title(): string
    {
        return 'Units';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $sheet->setSheetState(Worksheet::SHEETSTATE_VERYHIDDEN);
            },
        ];
    }
}