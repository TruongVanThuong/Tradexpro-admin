<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class IeoSaveRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $file_size = (ADMIN_SETTINGS_ARRAY['upload_max_size'] ?? 2) * 1024;

        return [
            'name' => 'required|max:100',
            'value' => 'required|numeric|gt:0',
            'symbol' => 'required|max:10|alpha',
            'total_supply' => 'required|numeric|min:1',
            'max_rate' => 'nullable|numeric|gte:0',
            'start_date' => 'required|date|before_or_equal:end_date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'ieo_icon' => [
                'nullable',
                'image',
                'mimes:jpg,png,jpeg,JPG,PNG,webp,gif',
                'max:' . $file_size,
            ],
        ];
    }

    /**
     * Custom error messages.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'name.required' => __('The name field is required.'),
            'name.max' => __('The name may not be greater than 100 characters.'),
            'value.required' => __('The value field is required.'),
            'value.numeric' => __('The value must be a valid number.'),
            'value.gt' => __('The value must be greater than 0.'),
            'symbol.required' => __('The symbol field is required.'),
            'symbol.max' => __('The symbol may not be greater than 10 characters.'),
            'symbol.alpha' => __('The symbol may only contain alphabetic characters.'),
            'total_supply.required' => __('The total supply field is required.'),
            'total_supply.numeric' => __('The total supply must be a valid number.'),
            'total_supply.min' => __('The total supply must be at least 1.'),
            'max_rate.numeric' => __('The max rate must be a valid number.'),
            'max_rate.gte' => __('The max rate must be greater than or equal to 0.'),
            'start_date.required' => __('The start date field is required.'),
            'start_date.date' => __('The start date must be a valid date.'),
            'start_date.before_or_equal' => __('The start date must be before or equal to the end date.'),
            'end_date.required' => __('The end date field is required.'),
            'end_date.date' => __('The end date must be a valid date.'),
            'end_date.after_or_equal' => __('The end date must be after or equal to the start date.'),
            'ieo_icon.image' => __('The file must be an image.'),
            'ieo_icon.mimes' => __('The image must be a file of type: jpg, jpeg, png, webp, gif.'),
            'ieo_icon.max' => __('The image size must not exceed :max KB.', ['max' => ADMIN_SETTINGS_ARRAY['upload_max_size'] ?? 2]),
        ];
    }
}
