<?php

namespace App\Exports\ProductImportSheets;

use App\Models\Brand;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BrandSheet implements FromArray, WithTitle, WithEvents
{
    public function array(): array
    {
        $brands = Brand::where('status', 1)->pluck('name')->toArray();
        return array_map(fn($name) => [$name], $brands);
    }

    public function title(): string
    {
        return 'Brands';
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