<?php

namespace App\Http\Controllers\RestAPI\v2\seller;

use App\Models\Shop;
use App\Models\Review;
use App\Models\Seller;
use App\Utils\Convert;
use App\Utils\Helpers;
use App\Models\Product;
use App\Models\DeliveryMan;
use App\Utils\ImageManager;
use Illuminate\Support\Str;
use App\Models\SellerWallet;
use App\Utils\BackEndHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\WithdrawRequest;
use App\Models\OrderTransaction;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class SellerController extends Controller
{
    public function shop_info(Request $request)
    {
        $data = Helpers::get_seller_by_token($request);

        if ($data['success'] == 1) {
            $seller = $data['data'];
            $product_ids = Product::where(['user_id' => $seller['id'], 'added_by' => 'seller'])->pluck('id')->toArray();
            $shop = Shop::with('governorates')->where(['seller_id' => $seller['id']])->first();
            $shop['rating'] = round(Review::whereIn('product_id', $product_ids)->avg('rating'), 3);
            $shop['rating_count'] = Review::whereIn('product_id', $product_ids)->count();
        } else {
            return response()->json([
                'auth-001' => translate('Your existing session token does not authorize you any more')
            ], 401);
        }

        return response()->json($shop, 200);
    }

    public function seller_delivery_man(Request $request)
    {
        $data = Helpers::get_seller_by_token($request);
        if ($data['success'] == 1) {
            $seller = $data['data'];
            $delivery_men = DeliveryMan::where(['seller_id' => $seller['id']])->get();
        } else {
            return response()->json([
                'auth-001' => translate('Your existing session token does not authorize you any more')
            ], 401);
        }

        return response()->json($delivery_men, 200);
    }

    public function shop_product_reviews(Request $request)
    {
        $data = Helpers::get_seller_by_token($request);

        if ($data['success'] == 1) {
            $seller = $data['data'];
            $product_ids = Product::where(['user_id' => $seller['id'], 'added_by' => 'seller'])->pluck('id')->toArray();
            $reviews = Review::whereIn('product_id', $product_ids)->with(['product', 'customer'])->get();
            $reviews->map(function ($data) {
                $data['attachment'] = json_decode($data['attachment'], true);
                return $data;
            });
        } else {
            return response()->json([
                'auth-001' => translate('Your existing session token does not authorize you any more')
            ], 401);
        }

        return response()->json($reviews, 200);
    }

    public function shop_product_reviews_status(Request $request)
    {
        $data = Helpers::get_seller_by_token($request);

        if ($data['success'] == 1) {
            $reviews = Review::find($request->id);
            $reviews->status = $request->status;
            $reviews->save();
            return response()->json(['message'=>translate('status updated successfully!!')],200);
        } else {
            return response()->json([
                'auth-001' => translate('Your existing session token does not authorize you any more')
            ], 401);
        }
    }

    public function seller_info(Request $request)
    {
        $data = Helpers::get_seller_by_token($request);

        if ($data['success'] == 1) {
            $seller = $data['data'];
        } else {
            return response()->json([
                'auth-001' => translate('Your existing session token does not authorize you any more')
            ], 401);
        }

        return response()->json(Seller::with(['wallet','governorates'])->withCount(['product', 'orders'])->where(['id' => $seller['id']])->first(), 200);
    }

    public function shop_info_update(Request $request)
    {
        // 1) Authenticate seller via token
        $result = Helpers::get_seller_by_token($request);
        if ($result['success'] !== 1) {
            return response()->json([
                'auth-001' => translate('Your session token is no longer valid')
            ], 401);
        }
        $sellerData = $result['data'];

        // 2) Retrieve the Shop model
        $shop = Shop::where('seller_id', $sellerData['id'])->firstOrFail();

        // 3) Handle shop image
        $oldImage = $shop->image;
        if ($request->hasFile('image')) {
            $imageName = ImageManager::update(
                'shop/',
                $oldImage,
                'webp',
                $request->file('image')
            );
        } else {
            $imageName = $oldImage;
        }

        // 4) Update shop attributes
        $shop->update([
            'name'       => $request->input('name'),
            'address'    => $request->input('address'),
            'contact'    => $request->input('contact'),
            'image'      => $imageName,
            'updated_at' => now(),
        ]);

        // 5) Sync governorates if provided
        if ($request->filled('governorate_ids')) {
            $ids = (array) $request->input('governorate_ids');
            $shop->governorates()->sync($ids);
        }

        // 6) Return success
        return response()->json(
            translate('Shop info updated successfully!'),
            200
        );
    }

    public function seller_info_update(Request $request)
    {
        // 1) Authenticate seller via token
        $result = Helpers::get_seller_by_token($request);
        if ($result['success'] !== 1) {
            return response()->json([
                'auth-001' => translate('Your session token is no longer valid')
            ], 401);
        }
        $seller = Seller::findOrFail($result['data']['id']);

        // 2) Handle profile image
        $oldImage = $seller->image;
        if ($request->hasFile('image')) {
            $imageName = ImageManager::update(
                'seller/',
                $oldImage,
                'webp',
                $request->file('image')
            );
        } else {
            $imageName = $oldImage;
        }

        // 3) Prepare new password (or keep existing)
        $newPassword = $request->filled('password')
            ? bcrypt($request->password)
            : $seller->password;

        // 4) Update seller attributes
        $seller->update([
            'f_name'      => $request->input('f_name'),
            'l_name'      => $request->input('l_name'),
            'bank_name'   => $request->input('bank_name'),
            'branch'      => $request->input('branch'),
            'account_no'  => $request->input('account_no'),
            'holder_name' => $request->input('holder_name'),
            'phone'       => $request->input('phone'),
            'password'    => $newPassword,
            'image'       => $imageName,
            'updated_at'  => now(),
        ]);

        // 5) Sync governorates (accepts single ID or array)
        if ($request->filled('governorate_ids')) {
            $ids = (array) $request->input('governorate_ids');
            $seller->governorates()->sync($ids);
        }

        // 6) Refresh auth token if password was changed
        if ($request->filled('password')) {
            $seller->update([
                'auth_token' => Str::random(50),
            ]);
        }

        // 7) Return success response
        return response()->json(
            translate('Info updated successfully!'),
            200
        );
    }

    public function withdraw_request(Request $request)
    {
        $data = Helpers::get_seller_by_token($request);
        if ($data['success'] == 1) {
            $seller = $data['data'];
        } else {
            return response()->json([
                'auth-001' => translate('Your existing session token does not authorize you any more')
            ], 401);
        }
        if($seller->account_no==null || $seller->bank_name==null)
        {
            return response()->json(['message'=>translate('Update your bank info first')], 202);
        }

        $wallet = SellerWallet::where('seller_id', $seller['id'])->first();
        if (($wallet->total_earning) >= Convert::usd($request['amount']) && $request['amount'] > 1) {
            DB::table('withdraw_requests')->insert([
                'seller_id' => $seller['id'],
                'amount' => Convert::usd($request['amount']),
                'transaction_note' => null,
                'approved' => 0,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            $wallet->total_earning -= BackEndHelper::currency_to_usd($request['amount']);
            $wallet->pending_withdraw += BackEndHelper::currency_to_usd($request['amount']);
            $wallet->save();
            return response()->json(translate('Withdraw request sent successfully!'), 200);
        }
        return response()->json(['message'=>translate('Invalid withdraw request')], 400);
    }

    public function close_withdraw_request(Request $request)
    {
        $data = Helpers::get_seller_by_token($request);
        if ($data['success'] == 1) {
            $seller = $data['data'];
        } else {
            return response()->json([
                'auth-001' => translate('Your existing session token does not authorize you any more')
            ], 401);
        }

        $withdraw_request = WithdrawRequest::find($request['id']);
        $wallet = SellerWallet::where('seller_id', $seller['id'])->first();

        if (isset($withdraw_request) && $withdraw_request->approved == 0) {
            $wallet->total_earning += BackEndHelper::currency_to_usd($withdraw_request['amount']);
            $wallet->pending_withdraw -= BackEndHelper::currency_to_usd($request['amount']);
            $wallet->save();
            $withdraw_request->delete();
            return response()->json(translate('Withdraw request has been closed successfully!'), 200);
        }

        return response()->json(translate('Withdraw request is invalid'), 400);
    }

    public function transaction(Request $request)
    {
        $data = Helpers::get_seller_by_token($request);
        if ($data['success'] == 1) {
            $seller = $data['data'];
        } else {
            return response()->json([
                'auth-001' => translate('Your existing session token does not authorize you any more')
            ], 401);
        }

        $transaction = WithdrawRequest::where('seller_id', $seller['id'])->latest()->get();

        return response()->json($transaction, 200);
    }

    public function monthly_earning(Request $request)
    {
        $data = Helpers::get_seller_by_token($request);
        if ($data['success'] == 1) {
            $seller = $data['data'];
        } else {
            return response()->json([
                'auth-001' => translate('Your existing session token does not authorize you any more')
            ], 401);
        }

        $from = \Carbon\Carbon::now()->startOfYear()->format('Y-m-d');
        $to = Carbon::now()->endOfYear()->format('Y-m-d');
        $seller_data = '';
        $seller_earnings = OrderTransaction::where([
            'seller_is' => 'seller',
            'seller_id' => $seller['id'],
            'status' => 'disburse'
        ])->select(
            DB::raw('IFNULL(sum(seller_amount),0) as sums'),
            DB::raw('YEAR(created_at) year, MONTH(created_at) month')
        )->whereBetween('created_at', [$from, $to])->groupby('year', 'month')->get()->toArray();
        for ($inc = 1; $inc <= 12; $inc++) {
            $default = 0;
            foreach ($seller_earnings as $match) {
                if ($match['month'] == $inc) {
                    $default = $match['sums'];
                }
            }
            $seller_data .= $default . ',';
        }

        return response()->json($seller_data, 200);
    }

    public function monthly_commission_given(Request $request)
    {
        $data = Helpers::get_seller_by_token($request);
        if ($data['success'] == 1) {
            $seller = $data['data'];
        } else {
            return response()->json([
                'auth-001' => translate('Your existing session token does not authorize you any more')
            ], 401);
        }

        $from = \Carbon\Carbon::now()->startOfYear()->format('Y-m-d');
        $to = Carbon::now()->endOfYear()->format('Y-m-d');

        $commission_data = '';
        $commission_earnings = OrderTransaction::where([
            'seller_is' => 'seller',
            'seller_id' => $seller['id'],
            'status' => 'disburse'
        ])->select(
            DB::raw('IFNULL(sum(admin_commission),0) as sums'),
            DB::raw('YEAR(created_at) year, MONTH(created_at) month')
        )->whereBetween('created_at', [$from, $to])->groupby('year', 'month')->get()->toArray();
        for ($inc = 1; $inc <= 12; $inc++) {
            $default = 0;
            foreach ($commission_earnings as $match) {
                if ($match['month'] == $inc) {
                    $default = $match['sums'];
                }
            }
            $commission_data .= $default . ',';
        }

        return response()->json($commission_data, 200);
    }

    public function update_cm_firebase_token(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cm_firebase_token' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::validationErrorProcessor($validator)], 403);
        }
        $data = Helpers::get_seller_by_token($request);

        if ($data['success'] == 1) {
            $seller = $data['data'];
        } else {
            return response()->json([
                'auth-001' => translate('Your existing session token does not authorize you any more')
            ], 401);
        }

        DB::table('sellers')->where('id', $seller->id)->update([
            'cm_firebase_token' => $request['cm_firebase_token'],
        ]);

        return response()->json(['message' => translate('successfully updated!')], 200);
    }

    public function account_delete(Request $request)
    {
        $data = Helpers::get_seller_by_token($request);
        if ($data['success'] == 1) {
            $seller = $data['data'];
        }else {
            return response()->json([
                'auth-001' => translate('Your existing session token does not authorize you any more')
            ], 401);
        }

        if($seller->id){
            ImageManager::delete('/seller/' . $seller['image']);

            $seller->delete();
            return response()->json(['message' => translate('Your_account_deleted_successfully!!')],200);

        }else{
            return response()->json(['message' =>'access_denied!!'],403);
        }
    }
}
