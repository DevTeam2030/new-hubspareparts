<?php

namespace App\Http\Controllers\Customer;

use App\Models\User;
use App\Utils\Helpers;
use App\Http\Controllers\Controller;
use App\Models\ShippingAddress;
use App\Models\ShippingMethod;
use App\Models\CartShipping;
use App\Traits\CommonTrait;
use App\Utils\CartManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SystemController extends Controller
{
    use CommonTrait;

    public function setPaymentMethod($name): JsonResponse
    {
        if (auth('customer')->check() || session()->has('mobile_app_payment_customer_id')) {
            session()->put('payment_method', $name);
            return response()->json(['status' => 1]);
        }
        return response()->json(['status' => 0]);
    }

    public function setShippingMethod(Request $request): JsonResponse
    {
        if ($request['cart_group_id'] == 'all_cart_group') {
            foreach (CartManager::get_cart_group_ids() as $groupId) {
                $request['cart_group_id'] = $groupId;
                self::insertIntoCartShipping($request);
            }
        } else {
            self::insertIntoCartShipping($request);
        }
        return response()->json(['status' => 1]);
    }

    public static function insertIntoCartShipping($request): void
    {
        $shipping = CartShipping::where(['cart_group_id' => $request['cart_group_id']])->first();
        if (isset($shipping) == false) {
            $shipping = new CartShipping();
        }
        $shipping['cart_group_id'] = $request['cart_group_id'];
        $shipping['shipping_method_id'] = $request['id'];
        $shipping['shipping_cost'] = ShippingMethod::find($request['id'])->cost;
        $shipping->save();
    }

    /*
     * default theme
     * @return json
     */
    public function getChooseShippingAddress(Request $request): JsonResponse
    {

        $zip_restrict_status = getWebConfig(name: 'delivery_zip_code_area_restriction');
        $country_restrict_status = getWebConfig(name: 'delivery_country_restriction');

        $physical_product = $request['physical_product'];
        $shipping = [];
        $billing = [];

        parse_str($request['shipping'], $shipping);
        parse_str($request['billing'], $billing);
        $is_guest = !auth('customer')->check();

        if (isset($shipping['save_address']) && $shipping['save_address'] == 'on') {

            if ($shipping['contact_person_name'] == null || $shipping['address'] == null || $shipping['city'] == null  || ($is_guest && $shipping['email'] == null)) {
                return response()->json([
                    'errors' => translate('Fill_all_required_fields_of_shipping_address')
                ], 403);
            }
            elseif ($zip_restrict_status && !self::delivery_zipcode_exist_check($shipping['zip'])) {
                return response()->json([
                    'errors' => translate('Delivery_unavailable_in_this_zip_code_area')
                ], 403);
            }

            $address_id = DB::table('shipping_addresses')->insertGetId([
                'customer_id' => auth('customer')->id() ?? ((session()->has('guest_id') ? session('guest_id'):0)),
                'is_guest' => auth('customer')->check() ? 0 : (session()->has('guest_id') ? 1:0),
                'contact_person_name' => $shipping['contact_person_name'],
                'address_type' => $shipping['address_type'],
                'address' => $shipping['address'],
                'city' => $shipping['city'],
                'zip' => $shipping['zip'],
                'country' => $shipping['country'],
                'phone' => $shipping['phone'],
                'email' => auth('customer')->check() ? null : $shipping['email'],
                'latitude' => $shipping['latitude'],
                'longitude' => $shipping['longitude'],
                'is_billing' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

        }
        else if (isset($shipping['shipping_method_id']) && $shipping['shipping_method_id'] == 0) {

            if ($shipping['contact_person_name'] == null || $shipping['address'] == null || $shipping['city'] == null || ($is_guest && $shipping['email'] == null)) {
                return response()->json([
                    'errors' => translate('Fill_all_required_fields_of_shipping/billing_address')
                ], 403);
            }
            elseif ($country_restrict_status && !self::delivery_country_exist_check($shipping['country'])) {
                return response()->json([
                    'errors' => translate('Delivery_unavailable_in_this_country')
                ], 403);
            }
            elseif ($zip_restrict_status && !self::delivery_zipcode_exist_check($shipping['zip'])) {
                return response()->json([
                    'errors' => translate('Delivery_unavailable_in_this_zip_code_area')
                ], 403);
            }

            $address_id = DB::table('shipping_addresses')->insertGetId([
                'customer_id' => auth('customer')->id() ?? ((session()->has('guest_id') ? session('guest_id'):0)),
                'is_guest' => auth('customer')->check() ? 0 : (session()->has('guest_id') ? 1:0),
                'contact_person_name' => $shipping['contact_person_name'],
                'address_type' => $shipping['address_type'],
                'address' => $shipping['address'],
                'city' => $shipping['city'],
                'zip' => $shipping['zip'],
                'country' => $shipping['country'],
                'phone' => $shipping['phone'],
                'email' => auth('customer')->check() ? null : $shipping['email'],
                'latitude' => $shipping['latitude'],
                'longitude' => $shipping['longitude'],
                'is_billing' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        else {
            if (isset($shipping['shipping_method_id'])) {
                $address = ShippingAddress::find($shipping['shipping_method_id']);
                if (!$address->country || !$address->zip) {
                    return response()->json([
                        'errors' => translate('Please_update_country_and_zip_for_this_shipping_address')
                    ], 403);
                }
                elseif ($country_restrict_status && !self::delivery_country_exist_check($address->country)) {
                    return response()->json([
                        'errors' => translate('Delivery_unavailable_in_this_country')
                    ], 403);
                }
                elseif ($zip_restrict_status && !self::delivery_zipcode_exist_check($address->zip)) {
                    return response()->json([
                        'errors' => translate('Delivery_unavailable_in_this_zip_code_area')
                    ], 403);
                }
                $address_id = $shipping['shipping_method_id'];
            }else{
                $address_id =  0;
            }
        }

        if ($request->billing_addresss_same_shipping == 'false') {
            if (isset($billing['save_address_billing']) && $billing['save_address_billing'] == 'on') {

                if ($billing['billing_contact_person_name'] == null || $billing['billing_address'] == null || $billing['billing_city'] == null|| $billing['billing_zip'] == null || $billing['billing_country'] == null || ($is_guest && $billing['billing_contact_email'] == null)) {
                    return response()->json([
                        'errors' => translate('Fill_all_required_fields_of_billing_address')
                    ], 403);
                }
                elseif ($country_restrict_status && !self::delivery_country_exist_check($billing['billing_country'])) {
                    return response()->json([
                        'errors' => translate('Delivery_unavailable_in_this_country')
                    ], 403);
                }
                elseif ($zip_restrict_status && !self::delivery_zipcode_exist_check($billing['billing_zip'])) {
                    return response()->json([
                        'errors' => translate('Delivery_unavailable_in_this_zip_code_area')
                    ], 403);
                }

                $billing_address_id = DB::table('shipping_addresses')->insertGetId([
                    'customer_id' => auth('customer')->id() ?? ((session()->has('guest_id') ? session('guest_id'):0)),
                    'is_guest' => auth('customer')->check() ? 0 : (session()->has('guest_id') ? 1:0),
                    'contact_person_name' => $billing['billing_contact_person_name'],
                    'address_type' => $billing['billing_address_type'],
                    'address' => $billing['billing_address'],
                    'city' => $billing['billing_city'],
                    'zip' => $billing['billing_zip'],
                    'country' => $billing['billing_country'],
                    'phone' => $billing['billing_phone'],
                    'email' => auth('customer')->check() ? null : $billing['billing_contact_email'],
                    'latitude' => $billing['billing_latitude'],
                    'longitude' => $billing['billing_longitude'],
                    'is_billing' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);


            }
            elseif ($billing['billing_method_id'] == 0) {

                if ($billing['billing_contact_person_name'] == null || $billing['billing_address'] == null || $billing['billing_city'] == null || $billing['billing_zip'] == null || $billing['billing_country'] == null || ($is_guest && $billing['billing_contact_email'] == null)) {
                    return response()->json([
                        'errors' => translate('Fill_all_required_fields_of_billing_address')
                    ], 403);
                }
                elseif ($country_restrict_status && !self::delivery_country_exist_check($billing['billing_country'])) {
                    return response()->json([
                        'errors' => translate('Delivery_unavailable_in_this_country')
                    ], 403);
                }
                elseif ($zip_restrict_status && !self::delivery_zipcode_exist_check($billing['billing_zip'])) {
                    return response()->json([
                        'errors' => translate('Delivery_unavailable_in_this_zip_code_area')
                    ], 403);
                }

                $billing_address_id = DB::table('shipping_addresses')->insertGetId([
                    'customer_id' => auth('customer')->id() ?? ((session()->has('guest_id') ? session('guest_id'):0)),
                    'is_guest' => auth('customer')->check() ? 0 : (session()->has('guest_id') ? 1:0),
                    'contact_person_name' => $billing['billing_contact_person_name'],
                    'address_type' => $billing['billing_address_type'],
                    'address' => $billing['billing_address'],
                    'city' => $billing['billing_city'],
                    'zip' => $billing['billing_zip'],
                    'country' => $billing['billing_country'],
                    'phone' => $billing['billing_phone'],
                    'email' => auth('customer')->check() ? null : $billing['billing_contact_email'],
                    'latitude' => $billing['billing_latitude'],
                    'longitude' => $billing['billing_longitude'],
                    'is_billing' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            else {
                $address = ShippingAddress::find($billing['billing_method_id']);
                if ($physical_product == 'yes') {
                    if (!$address->country || !$address->zip) {
                        return response()->json([
                            'errors' => translate('Update_country_and_zip_for_this_billing_address')
                        ], 403);
                    }
                    elseif ($country_restrict_status && !self::delivery_country_exist_check($address->country)) {
                        return response()->json([
                            'errors' => translate('Delivery_unavailable_in_this_country')
                        ], 403);
                    }
                    elseif ($zip_restrict_status && !self::delivery_zipcode_exist_check($address->zip)) {
                        return response()->json([
                            'errors' => translate('Delivery_unavailable_in_this_zip_code_area')
                        ], 403);
                    }
                }
                $billing_address_id = $billing['billing_method_id'];
            }
        }
        else {
            $billing_address_id = $address_id;
        }

        session()->put('address_id', $address_id);
        session()->put('billing_address_id', $billing_address_id);

        return response()->json([], 200);
    }

    /*
     * Except Default Theme
     * @return json
     */
    public function getChooseShippingAddressOther(Request $request): JsonResponse
    {
        $shipping = [];
        $billing = [];
        parse_str($request['shipping'], $shipping);
        parse_str($request['billing'], $billing);


        if (
            empty($shipping['contact_person_name']) ||
            empty($shipping['phone']) ||
            empty($shipping['governorate_id']) ||
            empty($shipping['city']) ||
            empty($shipping['address']) ||
            empty(session('selected_delivery_time'))
        ) {
            return response()->json([
                'errors' => translate('Please_fill_all_required_shipping_fields')
            ], 403);
        }


        $physicalProduct = $request['physical_product'];
        $zipRestrictStatus = getWebConfig(name: 'delivery_zip_code_area_restriction');
        $billingInputByCustomer = getWebConfig(name: 'billing_input_by_customer');
        $isGuestCustomer = !auth('customer')->check();

        // SHIPPING LOGIC
        $addressId = $shipping['shipping_method_id'] ?? 0;

        if (isset($shipping['save_address']) && $shipping['save_address'] === 'on') {
            $addressId = ShippingAddress::insertGetId([
                'customer_id'           => auth('customer')->id() ?? (session()->has('guest_id') ? session('guest_id') : 0),
                'is_guest'              => auth('customer')->check() ? 0 : (session()->has('guest_id') ? 1 : 0),
                'contact_person_name'   => $shipping['contact_person_name'] ?? '',
                'address_type'          => $shipping['address_type'] ?? 'home',
                'address'               => $shipping['address'] ?? '',
                'city'                  => $shipping['city'] ?? '',
                'zip'                   => $shipping['zip'] ?? '',
                'governorate_id'        => $shipping['governorate_id'] ?? null,
                'phone'                 => $shipping['phone'] ?? '',
                'latitude'              => $shipping['latitude'] ?? '',
                'longitude'             => $shipping['longitude'] ?? '',
                'email'                 => auth('customer')->check() ? null : ($shipping['email'] ?? null),
                'is_billing'            => 0,
            ]);
        } elseif (isset($shipping['update_address']) && $shipping['update_address'] === 'on') {
            $getShipping = ShippingAddress::find($addressId);
            if ($getShipping) {
                $getShipping->contact_person_name    = $shipping['contact_person_name'] ?? $getShipping->contact_person_name;
                $getShipping->address_type           = $shipping['address_type'] ?? $getShipping->address_type;
                $getShipping->address                = $shipping['address'] ?? $getShipping->address;
                $getShipping->city                   = $shipping['city'] ?? $getShipping->city;
                $getShipping->zip                    = $shipping['zip'] ?? $getShipping->zip;
                $getShipping->phone                  = $shipping['phone'] ?? $getShipping->phone;
                $getShipping->latitude               = $shipping['latitude'] ?? $getShipping->latitude;
                $getShipping->longitude              = $shipping['longitude'] ?? $getShipping->longitude;
                $getShipping->delivery_date          = $shipping['delivery_date'] ?? session('shipping_address.delivery_date');
                $getShipping->selected_delivery_time = $shipping['selected_delivery_time'] ?? session('shipping_address.selected_delivery_time');
                $getShipping->nearest_area_id        = $shipping['nearest_area_id'] ?? session('shipping_address.nearest_area_id');
                $getShipping->save();
            }
        } elseif (isset($shipping['shipping_method_id']) && !isset($shipping['update_address']) && !isset($shipping['save_address'])) {
            $addressId = ShippingAddress::insertGetId([
                'customer_id'           => auth('customer')->check() ? 0 : (session()->has('guest_id') ? session('guest_id') : 0),
                'is_guest'              => auth('customer')->check() ? 0 : (session()->has('guest_id') ? 1 : 0),
                'contact_person_name'   => $shipping['contact_person_name'] ?? '',
                'address_type'          => $shipping['address_type'] ?? 'home',
                'address'               => $shipping['address'] ?? '',
                'city'                  => $shipping['city'] ?? '',
                'zip'                   => $shipping['zip'] ?? '',
                'governorate_id'        => $shipping['governorate_id'] ?? null,
                'phone'                 => $shipping['phone'] ?? '',
                'email'                 => auth('customer')->check() ? null : ($shipping['email'] ?? null),
                'latitude'              => $shipping['latitude'] ?? '',
                'longitude'             => $shipping['longitude'] ?? '',
                'is_billing'            => 0,
            ]);
        }

        // BILLING LOGIC
        $billingAddressId = $addressId ?? 0;
        if ($request['billing_addresss_same_shipping'] == 'false' && isset($billing['billing_method_id']) && $billingInputByCustomer) {
            $billingAddressId = $billing['billing_method_id'];

            if (isset($billing['save_address_billing']) && $billing['save_address_billing'] === 'on') {
                $billingAddressId = ShippingAddress::insertGetId([
                    'customer_id'         => auth('customer')->id() ?? (session()->has('guest_id') ? session('guest_id') : 0),
                    'is_guest'            => auth('customer')->check() ? 0 : (session()->has('guest_id') ? 1 : 0),
                    'contact_person_name' => $billing['billing_contact_person_name'] ?? '',
                    'address_type'        => $billing['billing_address_type'] ?? 'home',
                    'address'             => $billing['billing_address'] ?? '',
                    'city'                => $billing['billing_city'] ?? '',
                    'zip'                 => $billing['billing_zip'] ?? '',
                    'governorate_id'      => $billing['billing_governorate_id'] ?? null,
                    'phone'               => $billing['billing_phone'] ?? '',
                    'email'               => auth('customer')->check() ? null : ($billing['billing_contact_email'] ?? null),
                    'latitude'            => $billing['billing_latitude'] ?? '',
                    'longitude'           => $billing['billing_longitude'] ?? '',
                    'is_billing'          => 1,
                ]);
            } elseif (isset($billing['update_billing_address']) && $billing['update_billing_address'] === 'on') {
                $getBilling = ShippingAddress::find($billingAddressId);
                if ($getBilling) {
                    $getBilling->contact_person_name = $billing['billing_contact_person_name'] ?? $getBilling->contact_person_name;
                    $getBilling->address_type        = $billing['billing_address_type'] ?? $getBilling->address_type;
                    $getBilling->address             = $billing['billing_address'] ?? $getBilling->address;
                    $getBilling->city                = $billing['billing_city'] ?? $getBilling->city;
                    $getBilling->zip                 = $billing['billing_zip'] ?? $getBilling->zip;
                    $getBilling->phone               = $billing['billing_phone'] ?? $getBilling->phone;
                    $getBilling->latitude            = $billing['billing_latitude'] ?? $getBilling->latitude;
                    $getBilling->longitude           = $billing['billing_longitude'] ?? $getBilling->longitude;
                    $getBilling->save();
                }
            } elseif (!isset($billing['update_billing_address']) && !isset($billing['save_address_billing'])) {
                $billingAddressId = ShippingAddress::insertGetId([
                    'customer_id'         => auth('customer')->check() ? 0 : (session()->has('guest_id') ? session('guest_id') : 0),
                    'is_guest'            => auth('customer')->check() ? 0 : (session()->has('guest_id') ? 1 : 0),
                    'contact_person_name' => $billing['billing_contact_person_name'] ?? '',
                    'address_type'        => $billing['billing_address_type'] ?? 'home',
                    'address'             => $billing['billing_address'] ?? '',
                    'city'                => $billing['billing_city'] ?? '',
                    'zip'                 => $billing['billing_zip'] ?? '',
                    'governorate_id'      => $billing['billing_governorate_id'] ?? null,
                    'phone'               => $billing['billing_phone'] ?? '',
                    'email'               => auth('customer')->check() ? null : ($billing['billing_contact_email'] ?? null),
                    'latitude'            => $billing['billing_latitude'] ?? '',
                    'longitude'           => $billing['billing_longitude'] ?? '',
                    'is_billing'          => 1,
                ]);
            }
        } elseif ($request['billing_addresss_same_shipping'] == 'false' && !isset($billing['billing_method_id']) && $physicalProduct != 'yes') {

        }

        // Save IDs in session
        session()->put('address_id', $addressId);
        session()->put('billing_address_id', $billingAddressId);


        if ($request['is_check_create_account'] && $isGuestCustomer) {
            $newCustomerAddress = $shipping;
            if (User::where('email', $newCustomerAddress['email'] ?? '')->orWhere('phone', $newCustomerAddress['phone'] ?? '')->first()) {

            } else {
                $newCustomerRegister = self::getRegisterNewCustomer(request: $request, address: $newCustomerAddress);
                session()->put('newCustomerRegister', $newCustomerRegister);
            }
        } else {
            session()->forget('newCustomerRegister');
            session()->forget('newRegisterCustomerInfo');
        }

        return response()->json([], 200);
    }

    function getRegisterNewCustomer($request, $address): array
    {
        return [
            'name' => $address['name'],
            'f_name' => $address['name'],
            'l_name' => '',
            'email' => $address['email'],
            'phone' => $address['phone'],
            'is_active' => 1,
            'password' => $address['password'],
            'referral_code' => Helpers::generate_referer_code(),
            'shipping_id' => session('address_id'),
            'billing_id' => session('billing_address_id'),
        ];
    }

}
