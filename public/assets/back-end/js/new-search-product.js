'use strict';
$('.search-product').on('keyup',function (){
    let name = $(this).val();
    if (name.length > 0) {
        let params = {searchValue: name};
        let categoryId = $('#category_id').val();
        let subCategoryId = $('#sub_category_id').val();
        let subSubCategoryId = $('#sub_sub_category_id').val();
        if (categoryId) params.category_id = categoryId;
        if (subCategoryId) params.sub_category_id = subCategoryId;
        if (subSubCategoryId) params.sub_sub_category_id = subSubCategoryId;
        $.get($('#get-search-product-route').data('action'), params, (response) => {
            $('.search-result-box').empty().html(response.result);
        })
    }
})
let selectProductSearch = $('.select-product-search');
selectProductSearch.on('click', '.select-product-item', function () {
    let productName = $(this).find('.product-name').text();
    let productId = $(this).find('.product-id').text();
    selectProductSearch.find('button.dropdown-toggle').text(productName);
    $('.product_id').val(productId);
})
