@extends('layouts.back-end.app-seller')

@section('title', translate('product_Bulk_Import'))

@section('content')
    <div class="content container-fluid">

        <div class="mb-4">
            <h2 class="h1 mb-1 text-capitalize d-flex gap-2">
                <img src="{{dynamicAsset(path: 'public/assets/back-end/img/bulk-import.png')}}" alt="">
                {{translate('product_bulk_import_excel')}}
            </h2>
        </div>

        <div class="row text-start">
            <div class="col-12">
                <div class="card card-body">
                    <h1 class="display-5">{{translate('instructions')}}: </h1>
                    <p>{{ translate('1') }}. {{translate('download_the_excel_file_and_fill_it_with_proper_data.')}}</p>

                    <p>{{ translate('2') }}. {{translate('you_can_download_the_excel_file_to_understand_how_the_data_must_be_filled.')}}</p>

                    <p>{{ translate('3') }}. {{translate('once_you_have_downloaded_and_filled_the_excel_file')}}, {{translate('upload_it_in_the_form_below_and_submit.')}}</p>

                    <p>4. {{translate('after_uploading_products_you_need_to_edit_them_and_set_product_images_and_choices.')}}</p>

                    <p>5. {{translate('you_can_select_brand_and_categories_from_their_dropdowns_in_the_excel_file.')}}</p>
                </div>
            </div>

            @if(session('failures') && count(session('failures')) > 0)
            <div class="col-12 mt-3">
                <div class="card">
                    <div class="card-header bg-warning">
                        <h4 class="mb-0 text-white">{{translate('import_warnings')}}</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>{{translate('row')}}</th>
                                        <th>{{translate('error')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach(session('failures') as $failure)
                                    <tr>
                                        <td>{{ $failure['row'] }}</td>
                                        <td>{{ $failure['error'] }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <div class="col-md-12 mt-2">
                <form class="product-form" action="{{route('vendor.products.post-bulk-import')}}" method="POST"
                      enctype="multipart/form-data">
                    @csrf
                    <div class="card rest-part">
                        <div class="px-3 py-4 d-flex flex-wrap align-items-center gap-10 justify-content-center">
                            <h4 class="mb-0">{{translate("do_not_have_the_excel_template")}}</h4>
                            <a href="{{route('vendor.products.download-excel-import-template')}}" download=""
                               class="btn-link text-capitalize fz-16 font-weight-medium">{{translate('download_here')}}</a>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <div class="row justify-content-center">
                                    <div class="col-auto">

                                        <div class="uploadDnD">
                                                <div class="form-group inputDnD input_image input_image_edit" data-title="{{translate('drag_&_drop_file_or_browse_file')}}">
                                                <input type="file" name="excel_file" accept=".xlsx, .xls" class="form-control-file text--primary font-weight-bold action-upload-section-dot-area" id="inputFile">
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <div class="d-flex flex-wrap gap-10 align-items-center justify-content-end">
                                <button type="reset" class="btn btn-secondary px-4 action-onclick-reload-page">{{translate('reset')}}</button>
                                <button type="button" class="btn btn--primary px-4 product-bulk-import-submit">{{translate('submit')}}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <span id="message-are-you-sure" data-text="{{ translate('are_you_sure') }}"></span>
    <span id="message-yes-word" data-text="{{ translate('yes') }}"></span>
    <span id="message-no-word" data-text="{{ translate('no') }}"></span>
    <span id="message-want-to-import-products" data-text="{{ translate('want_to_import_products') }}"></span>
@endsection

@push('script')
<script>
    $(document).ready(function() {
        let messageAreYouSure = $("#message-are-you-sure").data("text");
        let messageYesWord = $("#message-yes-word").data("text");
        let messageNoWord = $("#message-no-word").data("text");
        let messageWantToImportProducts = $("#message-want-to-import-products").data("text");

        $(".product-bulk-import-submit").on("click", function() {
            Swal.fire({
                title: messageAreYouSure,
                text: messageWantToImportProducts,
                type: "warning",
                showCancelButton: true,
                cancelButtonColor: "default",
                confirmButtonColor: "#377dff",
                cancelButtonText: messageNoWord,
                confirmButtonText: messageYesWord,
                reverseButtons: true,
            }).then((result) => {
                if (result.value) {
                    $(".product-form").submit();
                }
            });
        });
    });
</script>
@endpush
