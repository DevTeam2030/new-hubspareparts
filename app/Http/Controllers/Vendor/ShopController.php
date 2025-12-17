<?php

namespace App\Http\Controllers\Vendor;

use App\Contracts\Repositories\VendorRepositoryInterface;
use App\Contracts\Repositories\ShopRepositoryInterface;
use App\Enums\ViewPaths\Vendor\Shop;
use App\Http\Requests\Vendor\ShopRequest;
use App\Http\Requests\Vendor\ShopVacationRequest;
use App\Http\Controllers\BaseController;
use App\Models\Governorate;
use App\Services\ShopService;
use App\Services\VendorService;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

class ShopController extends BaseController
{
    public function __construct(
        private readonly VendorRepositoryInterface $vendorRepo,
        private readonly ShopRepositoryInterface $shopRepo,
        private readonly ShopService $shopService,
        private readonly VendorService $vendorService,
    )
    {
    }

    /**
     * @param Request|null $request
     * @param string|null $type
     * @return View|Collection|LengthAwarePaginator|callable|RedirectResponse|null
     */
    public function index(?Request $request, string $type = null): View|Collection|LengthAwarePaginator|null|callable|RedirectResponse
    {
        return $this->getView(request:$request , type:$type);
    }

    /**
     * @param Request|null $request
     * @param string|null $type
     * @return View|Collection|LengthAwarePaginator|callable|null
     */
    public function getView(?Request $request, string $type = null): View|Collection|LengthAwarePaginator|null|callable
    {
        $shop = $this->shopRepo->getFirstWhere(['seller_id' => auth('seller')->id()]);
        $vendor = $this->vendorRepo->getFirstWhere(params: ['id' => auth('seller')->id()]);
        if (!isset($shop)) {
            $this->shopRepo->add($this->shopService->getShopDataForAdd(vendor: $vendor));
            $shop = $this->shopRepo->getFirstWhere(['seller_id' => auth('seller')->id()]);
        }

        $minimumOrderAmountStatus = getWebConfig(name: 'minimum_order_amount_status');
        $minimumOrderAmountByVendor = getWebConfig(name: 'minimum_order_amount_by_seller');
        $freeDeliveryStatus = getWebConfig(name: 'free_delivery_status');
        $freeDeliveryResponsibility = getWebConfig(name: 'free_delivery_responsibility');

        if ($request['pagetype'] == 'order_settings' && (($minimumOrderAmountStatus && $minimumOrderAmountByVendor) || ($freeDeliveryStatus && $freeDeliveryResponsibility == 'seller'))) {
            return view(Shop::ORDER_SETTINGS[VIEW], compact('vendor', 'minimumOrderAmountStatus', 'minimumOrderAmountByVendor', 'freeDeliveryStatus', 'freeDeliveryResponsibility'));
        }
        return view(Shop::INDEX[VIEW], compact('shop','minimumOrderAmountStatus','minimumOrderAmountByVendor','freeDeliveryStatus','freeDeliveryResponsibility'));
     }

    /**
     * @param string|int $id
     * @return View
     */
    public function getUpdateView(string|int $id):View
     {
         $shop = $this->shopRepo->getFirstWhere(['id' => $id]);
         $governorates = Governorate::get();
         return view(Shop::UPDATE[VIEW],compact('shop','governorates'));
     }

    /**
     * @param ShopRequest $request
     * @param string|int $id
     * @return RedirectResponse
     */
    public function update(ShopRequest $request, string|int $id): RedirectResponse
    {
        // Get the shop based on the provided ID
        $shop = $this->shopRepo->getFirstWhere(['id' => $id]);

        // Get the updated shop data from the request
        $shopData = $this->shopService->getShopDataForUpdate(request: $request, shop: $shop);

        // Update the shop information in the database
        $this->shopRepo->update(id: $id, data: $shopData);

//        // Sync the governorates associated with the shop
//        // This will update the pivot table to reflect the selected governorates for the shop
//        $shop->governorates()->sync($request->governorate_ids);

        // Show a success message
        Toastr::info(translate('Shop_updated_successfully'));

        // Redirect back to the shop index page
        return redirect()->route(Shop::INDEX[ROUTE]);
    }

    /**
     * @param ShopVacationRequest $request
     * @param string|int $id
     * @return RedirectResponse
     */
    public function updateVacation(ShopVacationRequest $request , string|int $id):RedirectResponse
     {
         $this->shopRepo->update(id: $id, data: $this->shopService->getVacationData(request: $request));
         Toastr::success(translate('Vacation_mode_updated_successfully'));
         return redirect()->back();
     }

    /**
     * @param Request $request
     * @param string|int $id
     * @return JsonResponse
     */
    public function closeShopTemporary(Request $request, string|int $id):JsonResponse
     {
         $this->shopRepo->update(id: $id, data: ['temporary_close' => $request->get(key: 'status', default: 0)]);

         Cache::clear();
         return response()->json([
             'status' => true,
             'message' => $request['status'] ? translate("temporary_close_active_successfully") : translate("temporary_close_inactive_successfully"),
         ], status:200);
     }

    /**
     * @param Request $request
     * @param string|int $id
     * @return RedirectResponse
     */
    public function updateOrderSettings(Request $request, string|int $id):RedirectResponse
     {
         if ($request->has('minimum_order_amount')) {
             $this->vendorRepo->update(id: $id, data: $this->vendorService->getMinimumOrderAmount(request: $request));
         }
         if ($request->has('free_delivery_over_amount')) {
             $this->vendorRepo->update(
                 id: $id,
                 data: $this->vendorService->getFreeDeliveryOverAmountData(
                     request: $request
                 )
             );
         }
         return redirect()->back();
     }
}
