@if(isset($combinations) && count($combinations) > 0)
    <table class="table physical_product_show table-borderless">
        <thead class="thead-light thead-50 text-capitalize">
        <tr>
            <th class="text-center">
                <label for="" class="control-label">
                    {{ translate('SL') }}
                </label>
            </th>
            <th class="text-center">
                <label for="" class="control-label">
                    {{ translate('attribute_Variation') }}
                </label>
            </th>
            <th class="text-center">
                <label for="" class="control-label">
                    {{ translate('variation_Wise_Price') }}
                    ({{ getCurrencySymbol() }})
                </label>
            </th>
            <th class="text-center">
                <label for="" class="control-label">
                    {{ translate('SKU') }}
                </label>
            </th>
            <th class="text-center">
                <label for="" class="control-label">
                    {{ translate('Variation_Wise_Stock') }}
                </label>
            </th>
            <th class="text-center">
                <label class="control-label">{{ translate('Status') }}</label>
            </th>
        </tr>
        </thead>
        <tbody>

        @php
            $serial = 1;
        @endphp

        @foreach ($combinations as $key => $combination)
            <tr>
                <td class="text-center">
                    {{ $serial++ }}
                </td>
                <td>
                    <label for="" class="control-label">{{ $combination['type'] }}</label>
                    <input value="{{ $combination['type'] }}" name="type[]" class="d-none">
                </td>
                <td>
                    <input type="number" name="price_{{ $combination['type'] }}"
                           value="{{ $combination['price'] }}" min="0"
                           step="0.01"
                           class="form-control" required placeholder="{{ translate('ex').': 100'}}">
                </td>
                <td>
                    <input type="text" name="sku_{{ $combination['type'] }}" value="{{ $combination['sku'] }}"
                           class="form-control store-keeping-unit" required>
                </td>
                <td>
                    <input type="number" name="qty_{{ $combination['type'] }}"
                           value="{{ $combination['qty'] }}" min="1" max="100000" step="1"
                           class="form-control" placeholder="{{ translate('ex') }}: {{ translate('5') }}"
                           required>
                </td>
                <td class="text-center">
{{--                    <label class="switcher mx-auto-{{ $combination['type']}}">--}}
{{--                        <input type="checkbox"--}}
{{--                               name="status_{{ $combination['type'] }}"--}}
{{--                               value="1"--}}
{{--                            {{ ($combination['status'] == 0) ? '' : 'checked' }}>--}}
{{--                        <span class="switcher_control"></span>--}}
{{--                    </label>--}}

                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="1" name="status_{{ $combination['type'] }}" id="status_{{ $combination['type'] }}" checked>
                        <label class="form-check-label" for="status_{{ $combination['type'] }}">

                        </label>
                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endif
