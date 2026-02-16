@if(count($variations) > 0)
    <div class="card mt-3">
        <div class="card-header bg-light">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">{{ translate('Variation_Combinations') }}</h5>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-sm btn-outline-success" onclick="activateAllVariations()">
                        <i class="tio-checkmark-circle"></i> {{ translate('Activate_All') }}
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="deactivateAllVariations()">
                        <i class="tio-clear-circle"></i> {{ translate('Deactivate_All') }}
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-bordered mb-0 variation-table">
                    <thead class="thead-light">
                    <tr>
                        <th class="text-center" style="width: 60px;">{{ translate('SL') }}</th>
                        <th class="text-center" style="width: 120px;">
                            {{ translate('Status') }}
                            <span class="input-label-secondary cursor-pointer" data-toggle="tooltip"
                                  title="{{ translate('Enable_or_disable_this_variation_for_sale') }}">
                                <i class="tio-info"></i>
                            </span>
                        </th>
                        <th style="min-width: 200px;">{{ translate('Attribute_Variation') }}</th>
                        <th style="width: 200px;">
                            {{ translate('Variation_Wise_Price') }}
                            <span class="text-muted">({{ getCurrencySymbol(currencyCode: getCurrencyCode()) }})</span>
                        </th>
                        <th style="width: 250px;">{{ translate('SKU') }}</th>
                        <th style="width: 150px;">{{ translate('Stock') }}</th>
                        <th class="text-center" style="width: 80px;">{{ translate('Action') }}</th>
                    </tr>
                    </thead>
                    <tbody id="variation-table-body">
                    @foreach($variations as $variation)
                        <tr class="variation-row" data-variation-type="{{ $variation['type'] }}" data-row-index="{{ $variation['index'] }}">
                            <td class="text-center align-middle">
                                <span class="badge badge-soft-secondary">{{ $variation['index'] }}</span>
                            </td>
                            <td class="text-center align-middle">
                                <label class="switcher mx-auto">
                                    <input
                                        type="checkbox"
                                        class="switcher_input variation-status-toggle"
                                        name="variation_status[]"
                                        value="1"
                                        checked
                                        onchange="toggleVariationStatus(this)"
                                        data-variation-type="{{ $variation['type'] }}"
                                    >
                                    <span class="switcher_control"></span>
                                </label>
                                <input type="hidden" class="variation-status-hidden" name="variation_status_value[]" value="1">
                            </td>
                            <td class="align-middle">
                                <div class="d-flex align-items-center">
                                    <i class="tio-category mr-2 text-primary"></i>
                                    <strong class="variation-name">{{ $variation['type'] }}</strong>
                                    <span class="badge badge-soft-danger ml-2 inactive-badge" style="display: none;">
                                    {{ translate('Inactive') }}
                                </span>
                                </div>
                                <input type="hidden" name="variation_type[]" value="{{ $variation['type'] }}">
                            </td>
                            <td class="align-middle">
                                <input
                                    type="number"
                                    name="variation_price[]"
                                    class="form-control variation-price"
                                    placeholder="{{ translate('Ex') }}: 100"
                                    min="0"
                                    step="0.01"
                                    required
                                >
                            </td>
                            <td class="align-middle">
                                <input
                                    type="text"
                                    name="variation_sku[]"
                                    class="form-control variation-sku"
                                    value="-{{ $variation['type'] }}"
                                    placeholder="{{ translate('Enter_SKU') }}"
                                    required
                                >
                            </td>
                            <td class="align-middle">
                                <input
                                    type="number"
                                    name="variation_qty[]"
                                    class="form-control variation-qty"
                                    value="1"
                                    placeholder="{{ translate('Stock') }}"
                                    min="0"
                                    step="1"
                                    required
                                >
                            </td>
                            <td class="text-center align-middle">
                                <button
                                    type="button"
                                    class="btn btn-outline-danger btn-sm delete-variation-row-btn"
                                    onclick="deleteVariationRow('{{ $variation['type'] }}', this)"
                                    title="{{ translate('Delete_this_variation') }}"
                                >
                                    <i class="tio-delete"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-light">
            <div class="row align-items-center">
                <div class="col-sm-6">
                    <div class="d-flex gap-3">
                    <span class="text-muted">
                        <i class="tio-checkmark-circle text-success"></i>
                        {{ translate('Active') }}:
                        <strong id="active-variations-count">{{ count($variations) }}</strong>
                    </span>
                        <span class="text-muted">
                        <i class="tio-clear-circle text-secondary"></i>
                        {{ translate('Inactive') }}:
                        <strong id="inactive-variations-count">0</strong>
                    </span>
                        <span class="text-muted">
                        <i class="tio-category"></i>
                        {{ translate('Total') }}:
                        <strong id="total-variations-count">{{ count($variations) }}</strong>
                    </span>
                    </div>
                </div>
                <div class="col-sm-6 text-right">
                    <button
                        type="button"
                        class="btn btn-soft-danger btn-sm"
                        onclick="clearAllVariations()"
                    >
                        <i class="tio-clear"></i> {{ translate('Clear_All') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        /**
         * Toggle variation status (Active/Inactive)
         */
        function toggleVariationStatus(checkbox) {
            const row = $(checkbox).closest('.variation-row');
            const hiddenInput = row.find('.variation-status-hidden');
            const inactiveBadge = row.find('.inactive-badge');
            const variationType = $(checkbox).data('variation-type');

            if ($(checkbox).is(':checked')) {
                // Activate variation
                row.removeClass('table-secondary inactive-row');
                row.find('input:not(.variation-status-toggle):not(.variation-status-hidden)').prop('disabled', false);
                hiddenInput.val('1');
                inactiveBadge.hide();

                // Show success message
                if (typeof toastr !== 'undefined') {
                    toastr.success(`${variationType} activated`);
                }
            } else {
                // Deactivate variation
                row.addClass('table-secondary inactive-row');
                row.find('input:not(.variation-status-toggle):not(.variation-status-hidden)').prop('disabled', true);
                hiddenInput.val('0');
                inactiveBadge.show();

                // Show info message
                if (typeof toastr !== 'undefined') {
                    toastr.info(`${variationType} deactivated`);
                }
            }

            // Update counts
            updateStatusCounts();
        }

        /**
         * Activate all variations
         */
        function activateAllVariations() {
            $('.variation-status-toggle').prop('checked', true).each(function() {
                toggleVariationStatus(this);
            });

            if (typeof toastr !== 'undefined') {
                toastr.success('All variations activated');
            }
        }

        /**
         * Deactivate all variations
         */
        function deactivateAllVariations() {
            Swal.fire({
                title: '{{ translate("Deactivate_All") }}?',
                text: '{{ translate("This_will_deactivate_all_variations") }}',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#6c757d',
                cancelButtonColor: '#3085d6',
                confirmButtonText: '{{ translate("Yes_Deactivate") }}',
                cancelButtonText: '{{ translate("Cancel") }}'
            }).then((result) => {
                if (result.isConfirmed) {
                    $('.variation-status-toggle').prop('checked', false).each(function() {
                        toggleVariationStatus(this);
                    });

                    if (typeof toastr !== 'undefined') {
                        toastr.info('All variations deactivated');
                    }
                }
            });
        }

        /**
         * Update active/inactive counts
         */
        function updateStatusCounts() {
            const activeCount = $('.variation-status-toggle:checked').length;
            const totalCount = $('.variation-row').length;
            const inactiveCount = totalCount - activeCount;

            $('#active-variations-count').text(activeCount);
            $('#inactive-variations-count').text(inactiveCount);
            $('#total-variations-count').text(totalCount);
        }

        /**
         * Delete a specific variation row
         */
        function deleteVariationRow(variationType, button) {
            const totalRows = $('.variation-row').length;

            // Prevent deletion if it's the last row
            if (totalRows === 1) {
                Swal.fire({
                    icon: 'warning',
                    title: '{{ translate("Cannot_Delete") }}',
                    text: '{{ translate("At_least_one_variation_is_required") }}',
                    confirmButtonText: '{{ translate("OK") }}'
                });
                return;
            }

            Swal.fire({
                title: '{{ translate("Are_you_sure") }}?',
                html: `{{ translate("You_want_to_delete_this_variation") }}: <br><strong>${variationType}</strong>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: '<i class="tio-delete"></i> {{ translate("Yes_Delete") }}',
                cancelButtonText: '<i class="tio-clear"></i> {{ translate("Cancel") }}'
            }).then((result) => {
                if (result.isConfirmed) {
                    const row = $(button).closest('.variation-row');

                    row.addClass('table-danger');
                    setTimeout(() => {
                        row.fadeOut(400, function() {
                            $(this).remove();
                            renumberVariationRows();
                            updateStatusCounts();

                            if ($('.variation-row').length === 0) {
                                $('#sku_combination').html('');
                                if (typeof toastr !== 'undefined') {
                                    toastr.info('{{ translate("All_variations_removed") }}');
                                }
                            }
                        });
                    }, 300);

                    Swal.fire({
                        icon: 'success',
                        title: '{{ translate("Deleted") }}!',
                        text: '{{ translate("Variation_has_been_removed") }}',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            });
        }

        /**
         * Re-number variation rows after deletion
         */
        function renumberVariationRows() {
            $('.variation-row').each(function(index) {
                $(this).attr('data-row-index', index + 1);
                $(this).find('td:first .badge').text(index + 1);
            });
        }

        /**
         * Clear all variations
         */
        function clearAllVariations() {
            const totalRows = $('.variation-row').length;

            if (totalRows === 0) {
                if (typeof toastr !== 'undefined') {
                    toastr.info('{{ translate("No_variations_to_clear") }}');
                }
                return;
            }

            Swal.fire({
                title: '{{ translate("Clear_All_Variations") }}?',
                text: `{{ translate("This_will_remove_all") }} ${totalRows} {{ translate("variations") }}`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="tio-delete"></i> {{ translate("Yes_Clear_All") }}',
                cancelButtonText: '<i class="tio-clear"></i> {{ translate("Cancel") }}'
            }).then((result) => {
                if (result.isConfirmed) {
                    $('.variation-row').fadeOut(400, function() {
                        $('#sku_combination').html('');

                        Swal.fire({
                            icon: 'success',
                            title: '{{ translate("Cleared") }}!',
                            text: '{{ translate("All_variations_have_been_removed") }}',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    });
                }
            });
        }

        // Initialize on page load
        $(document).ready(function() {
            // Initialize tooltips
            $('[data-toggle="tooltip"]').tooltip();

            // Update counts on load
            updateStatusCounts();
        });
    </script>

    <style>
        /* Enhanced table styling */
        .variation-table {
            margin-bottom: 0;
        }

        .variation-table thead th {
            background-color: #f8f9fa;
            font-weight: 600;
            border-color: #dee2e6;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 12px 8px;
        }

        .variation-table tbody tr {
            transition: all 0.3s ease;
        }

        .variation-table tbody tr:hover {
            background-color: #f8f9fa !important;
        }

        .variation-row td {
            vertical-align: middle;
            padding: 10px 8px;
        }

        /* Inactive row styling */
        .variation-row.inactive-row {
            background-color: #f8f9fa !important;
            opacity: 0.7;
        }

        .variation-row.inactive-row .variation-name {
            text-decoration: line-through;
            color: #6c757d;
        }

        .variation-row.table-danger {
            background-color: #f8d7da !important;
            transition: background-color 0.3s ease;
        }

        /* Status toggle styling */
        .switcher {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 24px;
        }

        .switcher_input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .switcher_control {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 24px;
        }

        .switcher_control:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        .switcher_input:checked + .switcher_control {
            background-color: #28a745;
        }

        .switcher_input:checked + .switcher_control:before {
            transform: translateX(26px);
        }

        /* Badge styling */
        .badge-soft-danger {
            background-color: rgba(220, 53, 69, 0.1);
            color: #dc3545;
            font-weight: 600;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 11px;
        }

        .badge-soft-secondary {
            background-color: #e7eaf3;
            color: #677788;
            font-weight: 600;
            padding: 6px 10px;
            border-radius: 4px;
        }

        /* Button styling */
        .delete-variation-row-btn {
            padding: 6px 12px;
            border-radius: 4px;
            transition: all 0.3s ease;
            border: 1px solid #dc3545;
        }

        .delete-variation-row-btn:hover {
            background-color: #dc3545;
            color: white;
            transform: scale(1.05);
            box-shadow: 0 2px 4px rgba(220, 53, 69, 0.3);
        }

        .btn-soft-danger {
            color: #dc3545;
            background-color: rgba(220, 53, 69, 0.1);
            border: 1px solid rgba(220, 53, 69, 0.2);
        }

        .btn-soft-danger:hover {
            color: white;
            background-color: #dc3545;
            border-color: #dc3545;
        }

        /* Input field styling */
        .variation-price,
        .variation-sku,
        .variation-qty {
            font-size: 14px;
            padding: 8px 12px;
            border-radius: 4px;
        }

        .variation-price:focus,
        .variation-sku:focus,
        .variation-qty:focus {
            border-color: #80bdff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        .variation-row.inactive-row input:disabled {
            background-color: #e9ecef;
            cursor: not-allowed;
        }

        /* Card footer styling */
        .card-footer {
            padding: 15px 20px;
            background-color: #f8f9fa;
            border-top: 1px solid #dee2e6;
        }

        /* Animation for row deletion */
        @keyframes fadeOutRow {
            from {
                opacity: 1;
                transform: translateX(0);
            }
            to {
                opacity: 0;
                transform: translateX(-20px);
            }
        }

        .variation-row.removing {
            animation: fadeOutRow 0.4s ease-out;
        }
    </style>

@else
    <div class="alert alert-info text-center m-3">
        <i class="tio-info-outined"></i>
        {{ translate('No_variations_generated_yet') }}.
        <br>
        <small>{{ translate('Please_select_colors_or_attributes_and_enter_values_to_generate_variations') }}.</small>
    </div>
@endif
