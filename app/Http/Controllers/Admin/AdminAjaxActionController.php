<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\Repositories\BusinessSettingRepositoryInterface;
use App\Contracts\Repositories\FlashDealProductRepositoryInterface;
use App\Contracts\Repositories\FlashDealRepositoryInterface;
use App\Contracts\Repositories\ProductRepositoryInterface;
use App\Contracts\Repositories\StockClearanceProductRepositoryInterface;
use App\Contracts\Repositories\TranslationRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Request;
use App\Models\Category;

class AdminAjaxActionController extends Controller
{

    /**
     * @param ProductRepositoryInterface $productRepo
     */
    public function __construct(
        private readonly ProductRepositoryInterface          $productRepo,
        private readonly StockClearanceProductRepositoryInterface $stockClearanceProductRepo,
    ){}
    public function get_sub_category(Request $request)
    {
        // $request->category_id captures the value from the query string
        $sub_categories = Category::where(['parent_id' => $request->category_id])->get();

        return response()->json($sub_categories);
    }

    public function get_products(Request $request){
        $products = $this->productRepo->getListWithScope(
            scope: 'active',
            filters: [
                'category_id' => $request->category_id,
                'sub_category_id' => $request->sub_category_id,
                'sub_sub_category_id' => $request->sub_sub_category_id
            ],
            dataLimit: 'all'
        );
        return response()->json($products);
    }
    public function get_products_clearance(Request $request)
    {
        $clearanceProductIds = $this->stockClearanceProductRepo->getListWhere(filters: [ 'added_by' => 'admin' ]  )->pluck('product_id')->toArray();
        $products = $this->productRepo->getListWithScope(
            orderBy: ['id' => 'desc'],
            scope: "active",
            filters: [
                'added_by' => 'in_house',
                'category_id' => $request->category_id,
                'sub_category_id' => $request->sub_category_id,
                'sub_sub_category_id' => $request->sub_sub_category_id,
            ],
            whereNotIn: ['id' => $clearanceProductIds],
            relations: ['brand', 'category', 'seller.shop'],
            dataLimit: 'all');
        return response()->json($products);
    }
}
