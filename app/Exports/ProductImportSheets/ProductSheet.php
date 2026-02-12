<?php

namespace App\Exports\ProductImportSheets;

use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\NamedRange;

class ProductSheet implements FromArray, WithHeadings, WithTitle, WithEvents, WithStyles
{
    public function array(): array
    {
        return [];
    }

    public function headings(): array
    {
        return [
            'Name EN',
            'Name AR',
            'Short Description EN',
            'Short Description AR',
            'Description EN',
            'Description AR',
            'Category',
            'Sub Category',
            'Sub Sub Category',
            'Brand',
            'Product Type',
            'Product SKU',
            'Reference Number',
            'Shelf Number',
            'Unit',
            'Unit Price',
            'Minimum Order Qty',
            'Current Stock',
            'Discount Type',
            'Discount Amount',
            'Tax Amount',
            'Tax Calculation',
            'Shipping Cost',
            'Shipping Cost Multiply',
            'Youtube Video Link',
            'Meta Title',
            'Meta Description',
            'Tags',
        ];
    }

    public function title(): string
    {
        return 'Products';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $spreadsheet = $sheet->getParent();
                
                // Apply all dropdowns
                $this->applyCategoryDropdown($spreadsheet, $sheet);
                $this->applySubCategoryDropdown($spreadsheet, $sheet);
                $this->applyChildCategoryDropdown($spreadsheet, $sheet);
                $this->applyOtherDropdowns($spreadsheet, $sheet);
                
                // Hide helper sheets
                $this->hideHelperSheets($spreadsheet);
            },
        ];
    }
    
    private function applyCategoryDropdown($spreadsheet, Worksheet $productSheet)
    {
        $categorySheet = $spreadsheet->getSheetByName('Categories');
        if (!$categorySheet) {
            throw new \Exception("Categories worksheet not found");
        }
        
        $lastRow = $categorySheet->getHighestRow();
        
        // Named Range For Categories
        $spreadsheet->addNamedRange(
            new NamedRange('CategoryOptions', $categorySheet, '$A$1:$A$' . $lastRow)
        );
        
        // Apply dropdown On column G (category)
        for ($row = 2; $row <= 1000; $row++) {
            $validation = $productSheet->getCell('G' . $row)->getDataValidation();
            $validation->setType(DataValidation::TYPE_LIST);
            $validation->setFormula1('CategoryOptions');
            $validation->setShowDropDown(true);
            $validation->setAllowBlank(false);
            $validation->setShowErrorMessage(true);
            $validation->setErrorTitle('Invalid option');
            $validation->setError('Please select a valid category from the list.');
        }
    }
    
    private function applySubCategoryDropdown($spreadsheet, Worksheet $productSheet)
    {
        $subCategorySheet = $spreadsheet->getSheetByName('Sub Categories');
        if (!$subCategorySheet) {
            throw new \Exception("Sub categories worksheet not found");
        }
        
        // Create an array that collects all subcategories by category
        $categorySubMap = [];
        $lastSubRow = $subCategorySheet->getHighestRow();
        
        // Read data from Sub Categories sheet
        for ($j = 2; $j <= $lastSubRow; $j++) { // ابدأ من الصف 2
            $subCategoryName = $subCategorySheet->getCell('A' . $j)->getValue();
            $categoryName = $subCategorySheet->getCell('B' . $j)->getValue();
            
            if (!empty($categoryName) && !empty($subCategoryName)) {
                // Store the values as is without excessive normalization
                $categorySubMap[$categoryName][] = $subCategoryName;
            }
        }
        
        // Create a hidden sheet to store the data
        $mappingSheet = $spreadsheet->createSheet();
        $mappingSheet->setTitle('SubCatMapping');
        $mappingSheet->setSheetState(Worksheet::SHEETSTATE_VERYHIDDEN);
        
        // Create named ranges
        $rowCounter = 1;
        foreach ($categorySubMap as $categoryKey => $subcategories) {
            if (empty($categoryKey) || empty($subcategories)) {
                continue;
            }
            
            $normalizedKey = $this->simplifyKey($categoryKey);
            $rangeName = 'SubCat_' . $normalizedKey;
            
            // Write subcategories in column B
            $startRow = $rowCounter;
            foreach ($subcategories as $subcat) {
                $mappingSheet->setCellValue('B' . $rowCounter, $subcat);
                $rowCounter++;
            }
            $endRow = $rowCounter - 1;
            
            // Define named range
            $range = 'SubCatMapping!$B$' . $startRow . ':$B$' . $endRow;
            
            try {
                $spreadsheet->addNamedRange(
                    new NamedRange($rangeName, $mappingSheet, $range)
                );
            } catch (\Exception $e) {
                $rangeName = 'SubCat_' . md5($categoryKey);
                $spreadsheet->addNamedRange(
                    new NamedRange($rangeName, $mappingSheet, $range)
                );
            }
        }
        
        // Apply dropdown validation
        for ($row = 2; $row <= 1000; $row++) {
            $validation = $productSheet->getCell('H' . $row)->getDataValidation();
            $validation->setType(DataValidation::TYPE_LIST);
            
            // Simplified formula
            $formula = '=IFERROR(INDIRECT("SubCat_" & ';
            $formula .= 'SUBSTITUTE(TRIM(G' . $row . '), " ", "_")';
            $formula .= '), "")';
            
            $validation->setFormula1($formula);
            $validation->setShowDropDown(true);
            $validation->setAllowBlank(true);
            $validation->setShowErrorMessage(true);
            $validation->setErrorTitle('Invalid option');
            $validation->setError('Please select a valid category first, then choose from available sub-categories.');
        }
    }
        
    private function applyChildCategoryDropdown($spreadsheet, Worksheet $productSheet)
    {
        $childCategorySheet = $spreadsheet->getSheetByName('Child Categories');
        if (!$childCategorySheet) {
            throw new \Exception("Child categories worksheet not found");
        }
        
        // Step 1: Collect all Sub Categories names from Child Categories sheet
        $lastChildRow = $childCategorySheet->getHighestRow();
        
        // Store all Sub Categories in Child Categories sheet
        $allSubCategoriesInChildSheet = [];
        $childCategoriesBySub = [];
        
        for ($j = 2; $j <= $lastChildRow; $j++) {
            $childCategoryName = trim($childCategorySheet->getCell('A' . $j)->getValue());
            $subCategoryName = trim($childCategorySheet->getCell('B' . $j)->getValue());
            
            if (!empty($subCategoryName) && !empty($childCategoryName)) {
                // Store the original Sub Category name
                $allSubCategoriesInChildSheet[] = $subCategoryName;
                
                // Store the Child Category within the Sub Category
                if (!isset($childCategoriesBySub[$subCategoryName])) {
                    $childCategoriesBySub[$subCategoryName] = [];
                }
                $childCategoriesBySub[$subCategoryName][] = $childCategoryName;
            }
        }
        
        // Remove duplicates
        $allSubCategoriesInChildSheet = array_unique($allSubCategoriesInChildSheet);
        
        // Step 2: Create a hidden sheet for all Sub Categories
        $subCategoryReferenceSheet = $spreadsheet->createSheet();
        $subCategoryReferenceSheet->setTitle('SubCatReference');
        $subCategoryReferenceSheet->setSheetState(Worksheet::SHEETSTATE_VERYHIDDEN);
        
        // Write all Sub Categories in column A
        $subRefRow = 1;
        foreach ($allSubCategoriesInChildSheet as $subCat) {
            $subCategoryReferenceSheet->setCellValue('A' . $subRefRow, $subCat);
            $subRefRow++;
        }
        
        // Step 3: Create a hidden sheet for Child Categories
        $childMappingSheet = $spreadsheet->createSheet();
        $childMappingSheet->setTitle('ChildCatMapping');
        $childMappingSheet->setSheetState(Worksheet::SHEETSTATE_VERYHIDDEN);
        
        // Create named ranges based on Sub Categories names
        $rowCounter = 1;
        foreach ($childCategoriesBySub as $subCategoryName => $childCategories) {
            if (empty($subCategoryName) || empty($childCategories)) {
                continue;
            }
            
            // Create a safe key for the Named Range
            $rangeName = $this->createSafeRangeName($subCategoryName);
            
            // Write Child Categories in column B
            $startRow = $rowCounter;
            foreach ($childCategories as $childCat) {
                $childMappingSheet->setCellValue('B' . $rowCounter, $childCat);
                $rowCounter++;
            }
            $endRow = $rowCounter - 1;
            
            // Define named range
            $range = 'ChildCatMapping!$B$' . $startRow . ':$B$' . $endRow;
            
            try {
                $spreadsheet->addNamedRange(
                    new NamedRange($rangeName, $childMappingSheet, $range)
                );
                Log::info("Created named range: {$rangeName} for subcategory: {$subCategoryName}");
            } catch (\Exception $e) {
                // Use a fallback name in case of error
                $rangeName = 'CHILD_' . substr(md5($subCategoryName), 0, 8);
                $spreadsheet->addNamedRange(
                    new NamedRange($rangeName, $childMappingSheet, $range)
                );
            }
            
            // Store the relationship between the original Sub Category and the Named Range
            $this->storeSubCategoryMapping($spreadsheet, $subCategoryName, $rangeName);
        }
        
        // Step 4: Apply Data Validation with a refined formula
        $this->applyDynamicChildCategoryDropdown($spreadsheet, $productSheet);
    }

    private function createSafeRangeName($name)
    {
        // Simplify and safe the names
        $name = trim($name);
        
        // Replace special characters
        $replacements = [
            ' ' => '_',
            '.' => '_',
            '(' => '',
            ')' => '',
            '[' => '',
            ']' => '',
            '{' => '',
            '}' => '',
            '/' => '_',
            '\\' => '_',
            ':' => '',
            ';' => '',
            '\'' => '',
            '"' => '',
            '&' => 'and',
            '@' => 'at',
            '#' => 'no',
            '$' => 'dollar',
            '%' => 'percent',
            '^' => '',
            '*' => '',
            '+' => 'plus',
            '=' => 'equals',
            '?' => '',
            '!' => '',
            '~' => '',
            '`' => '',
            '|' => '',
            '<' => 'lt',
            '>' => 'gt',
        ];
        
        foreach ($replacements as $search => $replace) {
            $name = str_replace($search, $replace, $name);
        }
        
        // Remove all unwanted characters
        $name = preg_replace('/[^a-zA-Z0-9_]/', '', $name);
        
        // Remove consecutive underscores
        $name = preg_replace('/_+/', '_', $name);
        
        // Trim long names
        if (strlen($name) > 50) {
            $name = substr($name, 0, 50);
        }
        
        // Add a prefix
        $name = 'CHILD_' . $name;
        
        return $name;
    }

    private function storeSubCategoryMapping($spreadsheet, $subCategoryName, $rangeName)
    {
        // Create a sheet to store the mappings between Sub Categories and Named Ranges
        $mappingSheet = $spreadsheet->getSheetByName('SubCatToRangeMap');
        if (!$mappingSheet) {
            $mappingSheet = $spreadsheet->createSheet();
            $mappingSheet->setTitle('SubCatToRangeMap');
            $mappingSheet->setSheetState(Worksheet::SHEETSTATE_VERYHIDDEN);
            
            // Write titles
            $mappingSheet->setCellValue('A1', 'SubCategory');
            $mappingSheet->setCellValue('B1', 'RangeName');
        }
        
        // Find the next empty row
        $nextRow = $mappingSheet->getHighestRow() + 1;
        
        // Write data
        $mappingSheet->setCellValue('A' . $nextRow, $subCategoryName);
        $mappingSheet->setCellValue('B' . $nextRow, $rangeName);
    }

    private function applyDynamicChildCategoryDropdown($spreadsheet, Worksheet $productSheet)
    {
        // Use a dynamic Excel formula that uses VLOOKUP to find the appropriate Named Range
        for ($row = 2; $row <= 1000; $row++) {
            $validation = $productSheet->getCell('I' . $row)->getDataValidation();
            $validation->setType(DataValidation::TYPE_LIST);
            
            // Improved formula that uses VLOOKUP to search in the mapping table
            $formula = '=IFERROR(INDIRECT(';
            $formula .= 'VLOOKUP(TRIM(H' . $row . '), SubCatToRangeMap!$A:$B, 2, FALSE)';
            $formula .= '), "")';
            
            $validation->setFormula1($formula);
            $validation->setShowDropDown(true);
            $validation->setAllowBlank(true);
            $validation->setShowErrorMessage(true);
            $validation->setErrorTitle('Invalid option');
            $validation->setError('Please select a valid sub-category first, then choose from available child categories.');
        }
    }

    private function simplifyKey($key)
    {
        if (empty($key)) return '';
        
        // Simplify the key to match Excel
        $key = trim($key);
        
        // Replace only spaces and special characters
        $replacements = [
            ' ' => '_',
            '&' => 'and',
            '.' => '_',
            '(' => '_',
            ')' => '_',
            '/' => '_',
            '\\' => '_',
            '+' => '_',
            '-' => '_',
            "'" => '',
            '"' => '',
            '#' => '',
            '@' => '',
            '!' => '',
            '?' => '',
            '*' => '',
            '%' => '',
            '$' => '',
        ];
        
        foreach ($replacements as $search => $replace) {
            $key = str_replace($search, $replace, $key);
        }
        
        // Remove consecutive underscores
        $key = preg_replace('/_+/', '_', $key);
        $key = trim($key, '_');
        
        return $key;
    }
    
    private function applyOtherDropdowns($spreadsheet, $sheet)
    {
        // Brand Dropdown (Column J)
        $brandSheet = $spreadsheet->getSheetByName('Brands');
        if ($brandSheet) {
            $lastRow = $brandSheet->getHighestRow();
            for ($row = 2; $row <= 1000; $row++) {
                $validation = $sheet->getCell('J' . $row)->getDataValidation();
                $validation->setType(DataValidation::TYPE_LIST);
                $validation->setFormula1('Brands!$A$1:$A$' . $lastRow);
                $validation->setShowDropDown(true);
                $validation->setAllowBlank(true);
            }
        }
        
        // Unit Dropdown (Column O)
        $unitSheet = $spreadsheet->getSheetByName('Units');
        if ($unitSheet) {
            $lastRow = $unitSheet->getHighestRow();
            for ($row = 2; $row <= 1000; $row++) {
                $validation = $sheet->getCell('O' . $row)->getDataValidation();
                $validation->setType(DataValidation::TYPE_LIST);
                $validation->setFormula1('Units!$A$2:$A$' . $lastRow);
                $validation->setShowDropDown(true);
                $validation->setAllowBlank(false);
            }
        }
        
        // Fixed Dropdowns
        $fixedDropdowns = [
            'K' => ['values' => 'Physical', 'required' => true],
            'S' => ['values' => 'flat,percent', 'required' => true],
            'V' => ['values' => 'exclude,include', 'required' => true],
            'X' => ['values' => 'On,Off', 'required' => true],
        ];
        
        foreach ($fixedDropdowns as $column => $config) {
            for ($row = 2; $row <= 1000; $row++) {
                $validation = $sheet->getCell($column . $row)->getDataValidation();
                $validation->setType(DataValidation::TYPE_LIST);
                $validation->setFormula1('"' . $config['values'] . '"');
                $validation->setShowDropDown(true);
                $validation->setAllowBlank(!$config['required']);
            }
        }
    }
    
    private function hideHelperSheets($spreadsheet)
    {
        $sheetsToHide = [
            'Brands',
            'Units',
            'Categories',
            'Sub Categories',
            'Child Categories',
        ];
        foreach ($sheetsToHide as $sheetName) {
            $sheet = $spreadsheet->getSheetByName($sheetName);
            if ($sheet) {
                $sheet->setSheetState(Worksheet::SHEETSTATE_VERYHIDDEN);
            }
        }
    }

    public function styles(Worksheet $sheet)
    {
        // Header styling
        $sheet->getStyle('A1:AB1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 11,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '2D3748'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '4A5568'],
                ],
            ],
        ]);
        
        // Set column widths
        $columnWidths = [
            'A' => 40, 'B' => 40, 'C' => 40, 'D' => 40, 'E' => 40,
            'F' => 40, 'G' => 30, 'H' => 30, 'I' => 30, 'J' => 25,
            'K' => 20, 'L' => 30, 'M' => 25, 'N' => 25, 'O' => 20,
            'P' => 20, 'Q' => 30, 'R' => 20, 'S' => 20, 'T' => 25,
            'U' => 20, 'V' => 20, 'W' => 25, 'X' => 30, 'Y' => 30,
            'Z' => 30, 'AA' => 30, 'AB' => 40,
        ];

        foreach ($columnWidths as $column => $width) {
            $sheet->getColumnDimension($column)->setWidth($width);
        }

        // Set header row height
        $sheet->getRowDimension(1)->setRowHeight(25);
        
        // Freeze header row
        $sheet->freezePane('A2');
        
        // Set default row height for data rows
        for ($row = 2; $row <= 1000; $row++) {
            $sheet->getRowDimension($row)->setRowHeight(20);
        }
    }
}