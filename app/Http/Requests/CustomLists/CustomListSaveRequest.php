<?php

namespace App\Http\Requests\CustomLists;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\CustomList;

class CustomListSaveRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if (request()->has('id')) {
            $customList = CustomList::where('id', request()->get('id'))->where('user_id', auth()->user()->id)->get();
            if ($customList) {
                return true;
            }
            return false;
        }
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => 'required',
            'thumbnail' => ['mimes:jpeg,png', 'max:800'],
        ];
    }

    public function messages()
    {
        return [
            'thumbnail.max' => "Thumbnail size too big. 800KB Limit"
        ];
    }
}
