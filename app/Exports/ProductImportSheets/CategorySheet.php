<?php

namespace App\Exports\ProductImportSheets;

use App\Models\Category;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CategorySheet implements FromArray, WithTitle, WithEvents
{
    public function array(): array
    {
        $categories = Category::where('parent_id', 0)
            ->orderBy('name')
            ->pluck('name')
            ->toArray();
        
        return array_map(fn($name) => [$name], $categories);
    }
    
    public function title(): string
    {
        return 'Categories';
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