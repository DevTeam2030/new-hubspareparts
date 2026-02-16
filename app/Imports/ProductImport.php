<?php

namespace App\Imports;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductSeo;
use App\Models\Tag;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Throwable;

class ProductImport implements ToCollection, WithHeadingRow
{
    protected $failures = [];
    protected $categoryCache = [];

    public function collection(Collection $rows)
    {
        // Filter out completely empty rows
        $nonEmptyRows = $rows->filter(function ($row) {
            return collect($row)->filter(function ($cell) {
                return trim((string) $cell) !== '';
            })->isNotEmpty();
        });

        if ($nonEmptyRows->isEmpty()) {
            abort(400, translate('The_file_you_uploaded_does_not_contain_any_data_rows'));
        }

        // Build category cache for faster lookups
        $this->buildCategoryCache();

        foreach ($nonEmptyRows as $index => $row) {
            try {
                $rowNumber = $index + 2; // Account for header row

                // Required fields validation
                $requiredFields = [
                    'name_en',
                    'short_description_en',
                    'description_en',
                    'category',
                    'brand',
                    'product_type',
                    'product_sku',
                    'unit',
                    'unit_price',
                    'minimum_order_qty',
                    'current_stock',
                    'discount_amount',
                    'tax_calculation',
                    'shipping_cost',
                ];

                foreach ($requiredFields as $field) {
                    if (!isset($row[$field]) || trim($row[$field]) === '') {
                        $this->failures[] = [
                            'row' => $rowNumber,
                            'error' => str_replace(':field', $field, translate('Field_field_is_required_but_missing_or_empty')),
                        ];
                        continue 2; // Skip this row
                    }
                }

                // Generate slug (only for new products)
                $slug = Str::slug($row['name_en']) . '-' . Str::random(6);

                // Check if product already exists by SKU
                $existingProduct = Product::where('code', $row['product_sku'])->first();
                $isUpdate = $existingProduct !== null;

                // Get category values
                $categoryName = trim($row['category']);
                $subCategoryName = isset($row['sub_category']) && trim($row['sub_category']) ? trim($row['sub_category']) : null;
                $childCategoryName = isset($row['sub_sub_category']) && trim($row['sub_sub_category']) ? trim($row['sub_sub_category']) : null;

                // Validate category hierarchy
                if (!$this->validateCategoryHierarchy($categoryName, $subCategoryName, $childCategoryName, $rowNumber)) {
                    continue;
                }

                // Get related IDs
                $brandId = isset($row['brand']) && $row['brand'] ? $this->getBrandIdByName(trim($row['brand'])) : null;
                $categoryIds = $this->getCategoryIdsByName($categoryName, $subCategoryName, $childCategoryName);

                if (!$categoryIds['category_id']) {
                    $this->failures[] = [
                        'row' => $rowNumber,
                        'error' => str_replace(':name', $categoryName, translate('Category_name_not_found')),
                    ];
                    continue;
                }

                // Prepare category IDs array for JSON
                $categoryIdsArray = [];
                if ($categoryIds['category_id']) {
                    $categoryIdsArray[] = ['id' => (string)$categoryIds['category_id'], 'position' => 1];
                }
                if ($categoryIds['sub_category_id']) {
                    $categoryIdsArray[] = ['id' => (string)$categoryIds['sub_category_id'], 'position' => 2];
                }
                if ($categoryIds['sub_sub_category_id']) {
                    $categoryIdsArray[] = ['id' => (string)$categoryIds['sub_sub_category_id'], 'position' => 3];
                }

                // Prepare product data
                $data = [
                    'name' => $row['name_en'],
                    'added_by' => Auth::guard('admin')->check() ? 'admin' : 'seller',
                    'user_id' => Auth::guard('admin')->check() ? Auth::guard('admin')->id() : Auth::guard('seller')->id(),
                    'code' => $row['product_sku'],
                    'reference_number' => $row['reference_number'] ?? null,
                    'shelf_number' => $row['shelf_number'] ?? null,
                    'short_description' => isset($row['short_description_en']) && !empty(trim($row['short_description_en'])) ? '<p>' . trim($row['short_description_en']) . '</p>' : null,
                    'details' => isset($row['description_en']) && !empty(trim($row['description_en'])) ? '<p>' . trim($row['description_en']) . '</p>' : (isset($row['short_description_en']) && !empty(trim($row['short_description_en'])) ? '<p>' . trim($row['short_description_en']) . '</p>' : null),
                    'brand_id' => $brandId,
                    'unit' => $row['unit'],
                    'unit_price' => currencyConverter(amount: $row['unit_price']),
                    'purchase_price' => 0,
                    'tax' => isset($row['tax_amount']) && $row['tax_amount'] ? currencyConverter(amount: $row['tax_amount']) : 0,
                    'tax_type' => 'percent',
                    'tax_model' => $row['tax_calculation'] ?? 'exclude',
                    'discount' => isset($row['discount_amount']) && $row['discount_amount'] ? 
                        ((isset($row['discount_type']) && $row['discount_type'] == 'flat') ? currencyConverter(amount: $row['discount_amount']) : $row['discount_amount']) 
                        : 0,
                    'discount_type' => $row['discount_type'] ?? 'flat',
                    'product_type' => strtolower($row['product_type'] ?? 'physical'),
                    'digital_product_type' => null,
                    'category_ids' => json_encode($categoryIdsArray),
                    'category_id' => $categoryIds['category_id'],
                    'sub_category_id' => $categoryIds['sub_category_id'],
                    'sub_sub_category_id' => $categoryIds['sub_sub_category_id'],
                    'current_stock' => isset($row['current_stock']) && $row['current_stock'] ? abs($row['current_stock']) : 0,
                    'minimum_order_qty' => $row['minimum_order_qty'] ?? 1,
                    'video_provider' => 'youtube',
                    'video_url' => $row['youtube_video_link'] ?? null,
                    'colors' => json_encode([]),
                    'attributes' => json_encode([]),
                    'choice_options' => json_encode([]),
                    'variation' => json_encode([]),
                    'images' => json_encode([]),
                    'thumbnail' => 'def.png',
                    'thumbnail_storage_type' => 'public',
                    'color_image' => json_encode([]),
                    'status' => Auth::guard('admin')->check() ? 1 : 0,
                    'request_status' => Auth::guard('admin')->check() ? 1 : (getWebConfig(name: 'new_product_approval') == 1 ? 0 : 1),
                    'shipping_cost' => isset($row['shipping_cost']) && $row['shipping_cost'] ? currencyConverter(amount: $row['shipping_cost']) : 0,
                    'multiply_qty' => isset($row['shipping_cost_multiply']) && strtolower($row['shipping_cost_multiply']) == 'on' ? 1 : 0,
                    'meta_title' => $row['meta_title'] ?? null,
                    'meta_description' => $row['meta_description'] ?? null,
                    'meta_image' => null,
                    'digital_product_file_types' => [],
                    'digital_product_extensions' => [],
                    'updated_at' => now(),
                ];

                // Only add slug and created_at for new products
                if (!$isUpdate) {
                    $data['slug'] = $slug;
                    $data['created_at'] = now();
                }

                // Create or update product
                if ($isUpdate) {
                    $existingProduct->update($data);
                    $product = $existingProduct;
                } else {
                    $product = Product::create($data);
                }

                // Handle Tags if provided
                if (isset($row['tags']) && !empty(trim($row['tags']))) {
                    $tagIds = [];
                    $tags = explode(",", $row['tags']);
                    
                    foreach ($tags as $tagValue) {
                        $tagValue = trim($tagValue);
                        if (!empty($tagValue)) {
                            $tag = Tag::firstOrNew(['tag' => $tagValue]);
                            $tag->save();
                            $tagIds[] = $tag->id;
                        }
                    }
                    
                    if (!empty($tagIds)) {
                        $product->tags()->sync($tagIds);
                    }
                }

                // Handle Product SEO
                $seoData = [
                    'title' => $row['meta_title'] ?? null,
                    'description' => $row['meta_description'] ?? null,
                    'index' => '',
                    'no_follow' => '',
                    'no_image_index' => '',
                    'no_archive' => '',
                    'no_snippet' => 0,
                    'max_snippet' => 0,
                    'max_snippet_value' => 0,
                    'max_video_preview' => 0,
                    'max_video_preview_value' => 0,
                    'max_image_preview' => 0,
                    'max_image_preview_value' => 0,
                    'image' => null,
                    'updated_at' => now(),
                ];

                // Update or create SEO data
                ProductSeo::updateOrCreate(
                    ['product_id' => $product->id],
                    $seoData
                );

                // Handle translations for Arabic name if provided
                if (isset($row['name_ar']) && $row['name_ar']) {
                    $product->translations()->updateOrCreate(
                        [
                            'translationable_type' => 'App\Models\Product',
                            'translationable_id' => $product->id,
                            'locale' => 'eg',
                            'key' => 'name',
                        ],
                        [
                            'value' => $row['name_ar'],
                        ]
                    );
                }

                // Handle Arabic description if provided
                if (isset($row['description_ar']) && !empty(trim($row['description_ar']))) {
                    $product->translations()->updateOrCreate(
                        [
                            'translationable_type' => 'App\Models\Product',
                            'translationable_id' => $product->id,
                            'locale' => 'eg',
                            'key' => 'description',
                        ],
                        [
                            'value' => '<p>' . trim($row['description_ar']) . '</p>',
                        ]
                    );
                }

                // Handle Arabic short description if provided
                if (isset($row['short_description_ar']) && !empty(trim($row['short_description_ar']))) {
                    $product->translations()->updateOrCreate(
                        [
                            'translationable_type' => 'App\Models\Product',
                            'translationable_id' => $product->id,
                            'locale' => 'eg',
                            'key' => 'short_description',
                        ],
                        [
                            'value' => '<p>' . trim($row['short_description_ar']) . '</p>',
                        ]
                    );
                }

            } catch (Throwable $e) {
                $this->failures[] = [
                    'row' => $index + 2,
                    'error' => $e->getMessage()
                ];
            }
        }
    }

    /**
     * Build category cache for faster lookups
     */
    private function buildCategoryCache(): void
    {
        // Get all categories with loading relations
        $categories = Category::with(['childes' => function($query) {
            $query->with('childes');
        }])->get();
        
        foreach ($categories as $cat) {
            // Only main categories (parent_id = 0)
            if ($cat->parent_id == 0) {
                $this->categoryCache[$cat->name] = [
                    'id' => $cat->id,
                    'parent_id' => $cat->parent_id,
                    'children' => []
                ];
                
                // Sub categories
                foreach ($cat->childes as $subCat) {
                    $this->categoryCache[$cat->name]['children'][$subCat->name] = [
                        'id' => $subCat->id,
                        'parent_id' => $subCat->parent_id,
                        'children' => []
                    ];
                    
                    // Sub categories from the third level
                    foreach ($subCat->childes as $childCat) {
                        $this->categoryCache[$cat->name]['children'][$subCat->name]['children'][$childCat->name] = [
                            'id' => $childCat->id,
                            'parent_id' => $childCat->parent_id
                        ];
                    }
                }
            }
        }
    }

    /**
     * Validate category hierarchy
     */
    private function validateCategoryHierarchy($categoryName, $subCategoryName, $childCategoryName, $rowNumber): bool
    {
        // Check if main category exists
        if (!isset($this->categoryCache[$categoryName])) {
            $this->failures[] = [
                'row' => $rowNumber,
                'error' => str_replace(':cat', $categoryName, translate('Category_cat_not_found')),
            ];
            return false;
        }

        // Check if it's actually a main category (parent_id = 0)
        if ($this->categoryCache[$categoryName]['parent_id'] != 0) {
            $this->failures[] = [
                'row' => $rowNumber,
                'error' => str_replace(':cat', $categoryName, translate('cat_is_not_a_main_category_Please_select_a_main_category_from_the_dropdown')),
            ];
            return false;
        }

        // Validate sub-category if provided
        if ($subCategoryName) {
            if (!isset($this->categoryCache[$categoryName]['children'][$subCategoryName])) {
                $this->failures[] = [
                    'row' => $rowNumber,
                    'error' => str_replace([':sub', ':cat'], [$subCategoryName, $categoryName], translate('Sub_category_sub_not_found_under_category_cat')),
                ];
                return false;
            }

            // Validate child category if provided
            if ($childCategoryName) {
                if (!isset($this->categoryCache[$categoryName]['children'][$subCategoryName]['children'][$childCategoryName])) {
                    $this->failures[] = [
                        'row' => $rowNumber,
                        'error' => str_replace([':child', ':sub'], [$childCategoryName, $subCategoryName], translate('Child_category_child_not_found_under_sub_category_sub')),
                    ];
                    return false;
                }
            }
        } elseif ($childCategoryName) {
            // Can't have child category without sub-category
            $this->failures[] = [
                'row' => $rowNumber,
                'error' => translate('You_must_select_a_sub_category_before_selecting_a_child_category'),
            ];
            return false;
        }

        return true;
    }

    /**
     * Get category IDs by names
     */
    private function getCategoryIdsByName($categoryName, $subCategoryName, $childCategoryName): array
    {
        $ids = [
            'category_id' => null,
            'sub_category_id' => null,
            'sub_sub_category_id' => null
        ];

        if (isset($this->categoryCache[$categoryName])) {
            $ids['category_id'] = $this->categoryCache[$categoryName]['id'];
            
            if ($subCategoryName && isset($this->categoryCache[$categoryName]['children'][$subCategoryName])) {
                $ids['sub_category_id'] = $this->categoryCache[$categoryName]['children'][$subCategoryName]['id'];
                
                if ($childCategoryName && isset($this->categoryCache[$categoryName]['children'][$subCategoryName]['children'][$childCategoryName])) {
                    $ids['sub_sub_category_id'] = $this->categoryCache[$categoryName]['children'][$subCategoryName]['children'][$childCategoryName]['id'];
                }
            }
        }

        return $ids;
    }

    /**
     * Get brand ID by name
     */
    private function getBrandIdByName($name)
    {
        return Brand::where('name', $name)->where('status', 1)->value('id');
    }

    /**
     * Get failures for reporting
     */
    public function getFailures(): array
    {
        return $this->failures;
    }
}