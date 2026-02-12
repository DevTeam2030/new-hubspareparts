<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\ProductImportSheets\BrandSheet;
use App\Exports\ProductImportSheets\UnitSheet;
use App\Exports\ProductImportSheets\CategorySheet;
use App\Exports\ProductImportSheets\SubCategorySheet;
use App\Exports\ProductImportSheets\ChildCategorySheet;
use App\Exports\ProductImportSheets\ProductSheet;

class ProductBlueprintExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            new BrandSheet(),           // Sheet 1: Brands
            new UnitSheet(),            // Sheet 2: Units
            new CategorySheet(),        // Sheet 3: Main Categories
            new SubCategorySheet(),     // Sheet 4: Sub Categories
            new ChildCategorySheet(),   // Sheet 5: Child Categories
            new ProductSheet(),         // Sheet 6: Products (Main Template)
        ];
    }
}