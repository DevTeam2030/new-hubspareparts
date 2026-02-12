<?php

namespace App\Exports\ProductImportSheets;

use App\Models\Category;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SubCategorySheet implements FromArray, WithTitle, WithHeadings, WithEvents
{
    public function array(): array
    {
        $categories = Category::where('parent_id', '!=', 0)->get();
        
        $groupedData = [];
        
        foreach ($categories as $category) {
            $parent = Category::find($category->parent_id);
            
            // Only include if parent exists and is a main category (parent_id = 0)
            if ($parent && $parent->parent_id == 0) {
                $key = $parent->id; // Use ID instead of name
                
                if (!isset($groupedData[$key])) {
                    $groupedData[$key] = [
                        'main_category_id' => $parent->id,
                        'main_category_name' => $parent->name,
                        'subs' => []
                    ];
                }
                $groupedData[$key]['subs'][] = [
                    'id' => $category->id,
                    'name' => $category->name
                ];
            }
        }
        
        // Sort by main category name
        uasort($groupedData, function($a, $b) {
            return strcmp($a['main_category_name'], $b['main_category_name']);
        });
        
        $data = [];
        
        foreach ($groupedData as $group) {
            // Sort sub categories alphabetically
            usort($group['subs'], function($a, $b) {
                return strcmp($a['name'], $b['name']);
            });
            
            foreach ($group['subs'] as $sub) {
                $data[] = [
                    $sub['name'],                    // Column A: Sub Category Name
                    $group['main_category_name'],    // Column B: Main Category Name
                    $sub['id'],                      // Column C: Sub Category ID
                    $group['main_category_id'],      // Column D: Main Category ID
                ];
            }
        }
        
        return $data;
    }

    public function headings(): array
    {
        return ['Sub Category', 'Main Category', 'Sub Category ID', 'Main Category ID'];
    }
    
    public function title(): string
    {
        return 'Sub Categories';
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