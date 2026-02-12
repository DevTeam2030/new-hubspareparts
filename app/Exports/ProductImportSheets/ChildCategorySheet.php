<?php

namespace App\Exports\ProductImportSheets;

use App\Models\Category;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ChildCategorySheet implements FromArray, WithTitle, WithHeadings, WithEvents
{
    public function array(): array
    {
        $categories = Category::where('parent_id', '!=', 0)->get();
        
        $groupedData = [];
        
        foreach ($categories as $category) {
            $parent = Category::find($category->parent_id);
            
            if ($parent) {
                $grandparent = Category::find($parent->parent_id);
                
                // Only include if grandparent exists and is a main category
                if ($grandparent && $grandparent->parent_id == 0) {
                    $key = $grandparent->name . '|' . $parent->name;
                    
                    if (!isset($groupedData[$key])) {
                        $groupedData[$key] = [
                            'main_category' => $grandparent->name,
                            'sub_category' => $parent->name,
                            'children' => []
                        ];
                    }
                    $groupedData[$key]['children'][] = $category->name;
                }
            }
        }
        
        // Sort by main category, then sub category
        ksort($groupedData);
        
        $data = [];
        
        foreach ($groupedData as $item) {
            // Sort children alphabetically
            sort($item['children']);
            
            foreach ($item['children'] as $child) {
                $data[] = [
                    $child,                  // Column A: Child Category Name
                    $item['sub_category'],   // Column B: Sub Category Name
                ];
            }
        }
        
        return $data;
    }
    
    public function headings(): array
    {
        return ['Child Category', 'Sub Category'];
    }
    
    public function title(): string
    {
        return 'Child Categories';
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