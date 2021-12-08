<?php

namespace App\Http\Requests\CustomLists;

use App\Models\CustomList;
use Illuminate\Foundation\Http\FormRequest;

class CustomListItemRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $customList = CustomList::where('id', request()->get('custom_list_id'))
            ->where('user_id', auth()->user()->id);
        if ($customList) {
            return true;
        }
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'custom_list_id' => 'required',
            'thumbnail' => ['mimes:jpeg,png', "max:800"],
        ];
    }

    public function messages()
    {
        return [
            'thumbnail.max' => "Thumbnail size too big. 800KB Limit"
        ];
    }
}
