<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSmartAdRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        if ($this->form_type == 'edit') {
            $image_rule = "nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240";
        } else {
            $image_rule = "required_if:adType,IMAGE|mimes:jpeg,png,jpg,gif,webp|max:10240";
        }
        return [
            'name'     => [
                'required',
                Rule::unique('smart_ads', 'name')->ignore($this->smartAd),
            ],
            "body"     => "required_if:adType,HTML",
            "image"    => $image_rule,
            "imageUrl" => "nullable|url",
        ];
    }
}
