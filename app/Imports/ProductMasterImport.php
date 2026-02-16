<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ProductMasterImport implements WithMultipleSheets
{
    protected $productImport;

    public function __construct(ProductImport $productImport)
    {
        $this->productImport = $productImport;
    }

    public function sheets(): array
    {
        // Return sheet index 5 which is the "Products" sheet (0-indexed)
        // Sheets: 0=Brands, 1=Units, 2=Categories, 3=Sub Categories, 4=Child Categories, 5=Products
        return [
            5 => $this->productImport,
        ];
    }
}

