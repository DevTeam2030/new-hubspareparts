<?php

namespace App\Http\Controllers\Web;

use App\Contracts\Repositories\AttributeRepositoryInterface;
use App\Contracts\Repositories\ProductCompareRepositoryInterface;
use App\Enums\GlobalConstant;
use App\Enums\SessionKey;
use App\Helpers\CompareHelper;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Web\ProductCompareRequest;
use App\Services\ProductCompareService;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductCompareController extends BaseController
{
    /**
     * @param ProductCompareRepositoryInterface $productCompareRepo
     * @param ProductCompareService $productCompareService
     * @param AttributeRepositoryInterface $attributeRepo
     */
    public function __construct(
        private readonly ProductCompareRepositoryInterface $productCompareRepo,
        private readonly ProductCompareService $productCompareService,
        private readonly AttributeRepositoryInterface $attributeRepo,
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
       return $this->getProductCompareListView();
    }

    /**
     * @return View
     */
    public function getProductCompareListView():View
    {
        $customerId = auth('customer')->id();
        $sessionId = session()->getId();

        if ($customerId) {
            // Logged-in user: get from database
            $compareLists = $this->productCompareRepo->getListWhere(
                orderBy: ['id' => 'desc'],
                filters: ['user_id' => $customerId, 'whereHas' => 'product'],
                relations: ['product', 'product.digitalVariation'],
                dataLimit: 10
            );
        } else {
            // Guest user: get from session
            $compareProductIds = CompareHelper::getSessionCompareIds();
            $compareLists = collect();

            if (!empty($compareProductIds)) {
                $products = \App\Models\Product::whereIn('id', $compareProductIds)
                    ->with(['digitalVariation', 'reviews'])
                    ->orderBy('id', 'desc')
                    ->get();
                // Create compare list objects from session data
                foreach ($products as $product) {
                    $compareLists->push((object)[
                        'id' => $product->id,
                        'product_id' => $product->id,
                        'user_id' => null,
                        'session_id' => $sessionId,
                        'product' => $product,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }
        }

        $attributes = [];
        if (theme_root_path() == GlobalConstant::THEME_LIFESTYLE) {
            $attributes = $this->attributeRepo->getList(
                orderBy: ['id' => 'desc'], dataLimit: 'all',
            );
        }

        return view(VIEW_FILE_NAMES['account_compare_list'], compact('compareLists','attributes'));
    }

    /**
     * @param ProductCompareRequest $request
     * @return JsonResponse|RedirectResponse
     * @note if ($request->ajax()) {this request come from product details and home page and other product related section }
     * @note if (!$request->ajax()) {this request come  from user profile compare list tab  }
     */
    public function add(ProductCompareRequest $request):JsonResponse|RedirectResponse
    {

        if ($request->ajax()) {
            if (auth('customer')->check()) {
                return $this->handleLoggedInUserAdd($request);
            } else {
                return $this->handleGuestUserAdd($request);
            }
        } else {
            if (auth('customer')->check()) {
                return $this->handleLoggedInUserAddNonAjax($request);
            } else {
                return $this->handleGuestUserAddNonAjax($request);
            }
        }
    }

    /**
     * Handle add to compare for logged-in users via AJAX
     */
    private function handleLoggedInUserAdd(ProductCompareRequest $request): JsonResponse
    {
        $customerId = auth('customer')->id();
        $compareList = $this->productCompareRepo->getFirstWhere(params: ['user_id' => $customerId, 'product_id' => $request['product_id']]);
        if ($compareList) {
            $this->productCompareRepo->delete(params: ['id' => $compareList['id']]);
            $compareLists = $this->productCompareRepo->getListWhere(
                orderBy: ['id' => 'desc'],
                filters: ['user_id' => $customerId, 'whereHas' => 'product'],
                relations: ['product'],
                dataLimit: 'all'
            );
            $compareProductIds = $compareLists->pluck('product_id')->toArray();
            session()->forget(SessionKey::PRODUCT_COMPARE_LIST);
            session()->put(SessionKey::PRODUCT_COMPARE_LIST, $compareProductIds);
            return response()->json([
                'status' => 0,
                'message' => translate("compare_list_Removed"),
                'value' => 2,
                'count' => $compareLists->count(),
                'product_count' => count($compareProductIds),
                'compare_product_ids' => $compareProductIds
            ]);
        } else {
            $compareLists = $this->productCompareRepo->getListWhere(
                orderBy: ['id' => 'asc'],
                filters: ['user_id' => $customerId, 'whereHas' => 'product'],
                dataLimit: 'all'
            );
            if ($compareLists->count() == 3) {
                $compareList = $compareLists->first();
                $this->productCompareRepo->delete(params: ['id' => $compareList['id']]);
            }
            $this->productCompareRepo->add(data: $this->productCompareService->getAddData(customerId: $customerId, productId: $request['product_id']));
            $compareLists = $this->productCompareRepo->getListWhere(
                orderBy: ['id' => 'desc'],
                filters: ['user_id' => $customerId, 'whereHas' => 'product'],
                relations: ['product'],
                dataLimit: 'all'
            );
            $compareProductIds = $compareLists->pluck('product_id')->toArray();
            session()->forget(SessionKey::PRODUCT_COMPARE_LIST);
            session()->put(SessionKey::PRODUCT_COMPARE_LIST, $compareProductIds);
            return response()->json([
                'status' => 1,
                'message' => translate("product_added_to_compare_list"),
                'value' => 1,
                'count' => $compareLists->count(),
                'id' => $request['product_id'],
                'product_count' => count($compareProductIds),
                'compare_product_ids' => $compareProductIds
            ]);
        }
    }

    /**
     * Handle add to compare for guest users via AJAX
     */
    private function handleGuestUserAdd(ProductCompareRequest $request): JsonResponse
    {
        $productId = $request['product_id'];
        $result = CompareHelper::addProductToSession($productId, 3);

        if ($result['action'] === 'removed') {
            return response()->json([
                'status' => 0,
                'message' => translate("compare_list_Removed"),
                'value' => 2,
                'count' => $result['count'],
                'product_count' => $result['count'],
                'compare_product_ids' => $result['product_ids']
            ]);
        } else {
            return response()->json([
                'status' => 1,
                'message' => translate("product_added_to_compare_list"),
                'value' => 1,
                'count' => $result['count'],
                'id' => $productId,
                'product_count' => $result['count'],
                'compare_product_ids' => $result['product_ids']
            ]);
        }
    }

    /**
     * Handle add to compare for logged-in users via non-AJAX
     */
    private function handleLoggedInUserAddNonAjax(ProductCompareRequest $request): RedirectResponse
    {
        $customerId = auth('customer')->id();
        $compareList = $this->productCompareRepo->getFirstWhere(params: ['user_id' => $customerId, 'product_id' => $request['product_id']]);
        if ($compareList) {
            return redirect()->back();
        } else {
            $compareLists = $this->productCompareRepo->getListWhere(
                orderBy: ['id' => 'asc'],
                filters: ['user_id' => $customerId, 'whereHas' => 'product'],
                dataLimit: 'all'
            );
            if ($compareLists->count() == 3) {
                $compareList = $compareLists->first();
                $this->productCompareRepo->delete(params: ['id' => $compareList['id']]);
            }

            $this->productCompareRepo->add(data: $this->productCompareService->getAddData(customerId: $customerId, productId: $request['product_id']));
            $compareLists = $this->productCompareRepo->getListWhere(
                orderBy: ['id' => 'desc'],
                filters: ['user_id' => $customerId, 'whereHas' => 'product'],
                relations: ['product'],
                dataLimit: 'all'
            );
            $compareProductIds = $compareLists->pluck('product_id')->toArray();
            session()->forget(SessionKey::PRODUCT_COMPARE_LIST);
            session()->put(SessionKey::PRODUCT_COMPARE_LIST, $compareProductIds);
        }
        return redirect()->back();
    }

    /**
     * Handle add to compare for guest users via non-AJAX
     */
    private function handleGuestUserAddNonAjax(ProductCompareRequest $request): RedirectResponse
    {
        $productId = $request['product_id'];
        CompareHelper::addProductToSession($productId, 3);

        return redirect()->back();
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function delete(Request $request):RedirectResponse
    {
        $id = $request->id;

        if (auth('customer')->check()) {
            // Logged-in user: delete from database
            $this->productCompareRepo->delete(params: ['id' => $id, 'user_id' => auth('customer')->id()]);
            $compareLists = $this->productCompareRepo->getListWhere(
                orderBy: ['id' => 'desc'],
                filters: ['user_id' => auth('customer')->id()],
                dataLimit: 'all'
            )->pluck('product_id')->toArray();
            session()->forget(SessionKey::PRODUCT_COMPARE_LIST);
            session()->put(SessionKey::PRODUCT_COMPARE_LIST, $compareLists);
        } else {
            // Guest user: remove from session
            $productId = $request->product_id ?? $id;
            if ($productId) {
                CompareHelper::removeProductFromSession($productId);
            }
        }

        return redirect()->back();
    }

    /**
     * @return RedirectResponse
     */
    public function deleteAllCompareProduct():RedirectResponse
    {
        if (auth('customer')->check()) {
            // Logged-in user: delete from database
            $customerId = auth('customer')->id();
            $this->productCompareRepo->delete(params: ['user_id' => $customerId]);
            $compareLists = $this->productCompareRepo->getListWhere(
                orderBy: ['id' => 'desc'],
                filters: ['user_id' => auth('customer')->id()],
                dataLimit: 'all'
            )->pluck('product_id')->toArray();
            session()->forget(SessionKey::PRODUCT_COMPARE_LIST);
            session()->put(SessionKey::PRODUCT_COMPARE_LIST, $compareLists);
        } else {
            // Guest user: clear session
            CompareHelper::clearSessionCompare();
        }

        return redirect()->back();
    }

    /**
     * Get compare count for both logged-in and guest users
     * @return JsonResponse
     */
    public function getCompareCount(): JsonResponse
    {
        if (auth('customer')->check()) {
            // Logged-in user: get from database
            $customerId = auth('customer')->id();
            $count = $this->productCompareRepo->getListWhere(
                orderBy: ['id' => 'desc'],
                filters: ['user_id' => $customerId, 'whereHas' => 'product'],
                dataLimit: 'all'
            )->count();
        } else {
            // Guest user: get from session
            $count = CompareHelper::getSessionCompareCount();
        }

        return response()->json([
            'count' => $count
        ]);
    }

    /**
     * Check if product is in compare list
     * @param Request $request
     * @return JsonResponse
     */
    public function checkCompare(Request $request): JsonResponse
    {
        $productId = $request->product_id;
        $isInCompare = false;

        if (auth('customer')->check()) {
            // Logged-in user: check database
            $customerId = auth('customer')->id();
            $compareList = $this->productCompareRepo->getFirstWhere(params: ['user_id' => $customerId, 'product_id' => $productId]);
            $isInCompare = !empty($compareList);
        } else {
            // Guest user: check session
            $isInCompare = CompareHelper::isProductInSessionCompare($productId);
        }

        return response()->json([
            'in_compare' => $isInCompare
        ]);
    }
}
