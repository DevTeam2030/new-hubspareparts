<?php

namespace App\Http\Requests\Vendor;

use Illuminate\Foundation\Http\FormRequest;

class ImportProductRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            "excel_file" => "required|file|mimes:xlsx|max:10240",
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    public function messages(): array
    {
        return [
            'excel_file.required' => translate('Please_upload_an_Excel_file_of_type_xlsx'),
            'excel_file.file' => translate('The_uploaded_item_must_be_a_valid_file'),
            'excel_file.mimes' => translate('The_file_must_be_an_Excel_file_of_type_xlsx'),
            'excel_file.max' => translate('The_file_size_must_not_exceed_10_MB'),
        ];
    }
}
