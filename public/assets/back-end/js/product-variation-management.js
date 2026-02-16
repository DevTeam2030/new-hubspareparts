/**
 * FIXED: Product Variation Management - Prevents Disappearing Inputs
 * File: public/assets/back-end/js/product-variation-management.js
 */

$(document).ready(function() {
    console.log('Variation management initialized');

    // Initialize variation controls
    initializeVariationControls();

    // Event listener for attribute selection - FIXED to prevent clearing
    $('#choice_attributes').on('change', function() {
        console.log('Attributes changed:', $(this).val());
        handleAttributeChange();
    });

    // Event listener for color switcher
    $('#product-color-switcher').on('change', function() {
        handleColorSwitcherChange();
    });

    // Event listener for color selection
    $('#colors-selector').on('change', function() {
        getVariantCombination();
    });
});

/**
 * Initialize variation controls
 */
function initializeVariationControls() {
    if ($.fn.select2) {
        $('.js-example-basic-multiple').select2({
            placeholder: 'Select options',
            allowClear: true
        });
    }
}

/**
 * Handle attribute selection change - FIXED VERSION
 */
function handleAttributeChange() {
    const selectedAttributes = $('#choice_attributes').val() || [];
    console.log('Selected attributes:', selectedAttributes);

    // Get currently displayed attribute IDs
    const currentAttributeIds = [];
    $('#customer_choice_options [data-choice-id]').each(function() {
        currentAttributeIds.push($(this).attr('data-choice-id'));
    });

    console.log('Current attribute IDs:', currentAttributeIds);

    // Remove attributes that are no longer selected
    currentAttributeIds.forEach(function(attrId) {
        if (!selectedAttributes.includes(attrId)) {
            console.log('Removing attribute:', attrId);
            $(`[data-choice-id="${attrId}"]`).remove();
        }
    });

    // Add new attributes
    selectedAttributes.forEach(function(attrId) {
        if (!currentAttributeIds.includes(attrId)) {
            console.log('Adding attribute:', attrId);
            const attributeName = $('#choice_attributes option[value="' + attrId + '"]').text().trim();
            addAttributeChoiceDirectly(attrId, attributeName);
        }
    });

    // Only regenerate if we have changes
    if (selectedAttributes.length > 0 || currentAttributeIds.length > 0) {
        // Small delay to ensure inputs are ready
        setTimeout(function() {
            getVariantCombination();
        }, 300);
    }
}

/**
 * Add attribute choice input WITHOUT AJAX call - FIXED VERSION
 */
function addAttributeChoiceDirectly(attributeId, attributeName) {
    // Check if already exists
    if ($(`[data-choice-id="${attributeId}"]`).length > 0) {
        console.log('Attribute already exists:', attributeId);
        return;
    }

    console.log('Creating input for:', attributeName);

    const choiceHtml = `
        <div class="col-md-12 mb-3" data-choice-id="${attributeId}">
            <div class="choice-option-container" data-choice-name="choice_${attributeName}">
                <label class="title-color font-weight-bold mb-2">
                    <i class="tio-category"></i> ${attributeName}
                </label>
                <input
                    type="text"
                    class="form-control choice-input"
                    name="choice_${attributeName}[]"
                    placeholder="Enter values separated by comma (e.g., Power1, Power2)"
                    data-role="tagsinput"
                    id="choice_input_${attributeId}"
                    data-attribute-id="${attributeId}"
                    data-attribute-name="${attributeName}"
                />
            </div>
        </div>
    `;

    $('#customer_choice_options').append(choiceHtml);

    // Initialize tags input with delay to ensure DOM is ready
    setTimeout(function() {
        initializeTagsInput(attributeId, attributeName);
    }, 100);
}

/**
 * Handle color switcher change
 */
function handleColorSwitcherChange() {
    const isChecked = $('#product-color-switcher').is(':checked');

    if (isChecked) {
        $('#colors-selector').prop('disabled', false);
    } else {
        $('#colors-selector').prop('disabled', true).val(null).trigger('change');
    }

    getVariantCombination();
}

/**
 * Initialize tags input - FIXED VERSION
 */
function initializeTagsInput(attributeId, attributeName) {
    const inputId = `#choice_input_${attributeId}`;
    const $input = $(inputId);

    if ($input.length === 0) {
        console.error('Input not found:', inputId);
        return;
    }

    // Check if already initialized
    if ($input.hasClass('tagsinput-initialized')) {
        console.log('Already initialized:', inputId);
        return;
    }

    // Check if tagsinput plugin exists
    if (typeof $.fn.tagsinput === 'undefined') {
        console.warn('Bootstrap Tags Input plugin not loaded. Using fallback.');

        // Fallback: Simple comma-separated input
        $input.on('input change', function() {
            clearTimeout(window.variationTimeout);
            window.variationTimeout = setTimeout(function() {
                getVariantCombination();
            }, 500);
        });

        $input.addClass('tagsinput-initialized');
        return;
    }

    try {
        // Initialize bootstrap-tagsinput
        $input.tagsinput({
            confirmKeys: [13, 44, 188], // Enter, comma
            trimValue: true,
            tagClass: 'badge badge-info'
        });

        // Mark as initialized
        $input.addClass('tagsinput-initialized');

        // Handle tag added event
        $input.on('itemAdded', function(event) {
            console.log('Tag added:', event.item);
            clearTimeout(window.variationTimeout);
            window.variationTimeout = setTimeout(function() {
                getVariantCombination();
            }, 300);
        });

        // Handle tag removed event
        $input.on('itemRemoved', function(event) {
            console.log('Tag removed:', event.item);
            clearTimeout(window.variationTimeout);
            window.variationTimeout = setTimeout(function() {
                getVariantCombination();
            }, 300);
        });

        console.log('Tags input initialized for:', attributeName);

    } catch (error) {
        console.error('Error initializing tagsinput:', error);

        // Fallback to simple input
        $input.on('input change', function() {
            clearTimeout(window.variationTimeout);
            window.variationTimeout = setTimeout(function() {
                getVariantCombination();
            }, 500);
        });

        $input.addClass('tagsinput-initialized');
    }
}

/**
 * Generate variant combinations
 */
function getVariantCombination() {
    console.log('Generating variant combinations...');

    // Get selected colors
    const colors = $('#colors-selector').val() || [];
    console.log('Colors:', colors);

    // Get choice options
    const choiceOptions = {};
    let hasChoices = false;

    $('.choice-input').each(function() {
        const $this = $(this);
        const attributeName = $this.data('attribute-name');
        const values = $this.val();

        console.log('Processing attribute:', attributeName, 'Values:', values);

        if (values && values.trim() !== '') {
            const valuesArray = values.split(',').map(v => v.trim()).filter(v => v !== '');
            if (valuesArray.length > 0) {
                choiceOptions[attributeName] = valuesArray;
                hasChoices = true;
                console.log('Added choice:', attributeName, valuesArray);
            }
        }
    });

    console.log('Choice options:', choiceOptions);

    // Check if we have any variations to generate
    if (colors.length === 0 && !hasChoices) {
        console.log('No variations to generate');
        $('#sku_combination').html('');
        return;
    }

    // Prepare data for AJAX request
    const formData = new FormData();
    formData.append('colors', JSON.stringify(colors));
    formData.append('choice_options', JSON.stringify(choiceOptions));

    // Get CSRF token
    const token = $('meta[name="csrf-token"]').attr('content') || $('input[name="_token"]').val();

    if (!token) {
        console.error('CSRF token not found!');
        return;
    }

    // Get route URL
    const route = $('#route-admin-products-sku-combination').data('url');

    if (!route) {
        console.error('SKU combination route not found!');
        console.error('Make sure you have: <span id="route-admin-products-sku-combination" data-url="..."></span>');
        return;
    }

    console.log('Sending request to:', route);

    // Show loading state
    $('#sku_combination').html(`
        <div class="text-center p-5">
            <div class="spinner-border text-primary" role="status">
                <span class="sr-only">Loading...</span>
            </div>
            <p class="mt-3">Generating variations...</p>
        </div>
    `);

    // Send AJAX request
    $.ajax({
        url: route,
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        headers: {
            'X-CSRF-TOKEN': token
        },
        success: function(response) {
            console.log('Response received:', response);

            if (response.status === 'success') {
                $('#sku_combination').html(response.view);

                // Show success message
                if (typeof toastr !== 'undefined') {
                    toastr.success('Variations generated successfully!');
                }
            } else {
                console.error('Generation failed:', response);
                $('#sku_combination').html(`
                    <div class="alert alert-warning">
                        Failed to generate variations. Please try again.
                    </div>
                `);
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', error);
            console.error('Status:', status);
            console.error('Response:', xhr.responseText);

            let errorMessage = 'Error generating variations';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            } else if (xhr.status === 404) {
                errorMessage = 'Route not found. Make sure you added the sku-combination route.';
            } else if (xhr.status === 500) {
                errorMessage = 'Server error. Check Laravel logs.';
            }

            $('#sku_combination').html(`
                <div class="alert alert-danger">
                    <i class="tio-error"></i> ${errorMessage}
                    <br><small>Check browser console for details.</small>
                </div>
            `);

            if (typeof toastr !== 'undefined') {
                toastr.error('Failed to generate variations');
            }
        }
    });
}

/**
 * Validate variations before form submission
 */
function validateVariations() {
    const hasColors = $('#product-color-switcher').is(':checked') &&
        $('#colors-selector').val() &&
        $('#colors-selector').val().length > 0;

    const hasAttributes = $('.choice-input').length > 0 &&
        $('.choice-input').filter(function() {
            return $(this).val() && $(this).val().trim() !== '';
        }).length > 0;

    if (hasColors || hasAttributes) {
        const variationCount = $('.variation-row').length;

        if (variationCount === 0) {
            if (typeof toastr !== 'undefined') {
                toastr.error('Please generate variations before submitting');
            } else {
                alert('Please generate variations before submitting');
            }
            return false;
        }

        // Validate that all variation inputs are filled
        let hasEmptyInputs = false;
        let emptyFieldName = '';

        $('.variation-row').each(function() {
            const price = $(this).find('.variation-price').val();
            const sku = $(this).find('.variation-sku').val();
            const qty = $(this).find('.variation-qty').val();

            if (!price) {
                hasEmptyInputs = true;
                emptyFieldName = 'Price';
                return false;
            }
            if (!sku) {
                hasEmptyInputs = true;
                emptyFieldName = 'SKU';
                return false;
            }
            if (!qty && qty !== '0') {
                hasEmptyInputs = true;
                emptyFieldName = 'Stock';
                return false;
            }
        });

        if (hasEmptyInputs) {
            if (typeof toastr !== 'undefined') {
                toastr.error(`Please fill in ${emptyFieldName} for all variations`);
            } else {
                alert(`Please fill in ${emptyFieldName} for all variations`);
            }
            return false;
        }
    }

    return true;
}

// Add validation to form submission
$(document).on('click', '.product-add-requirements-check', function(e) {
    e.preventDefault();

    if (validateVariations()) {
        $('#product_form').submit();
    }
});
