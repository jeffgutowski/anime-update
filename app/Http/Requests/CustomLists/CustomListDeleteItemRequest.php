<?php

namespace App\Http\Requests\CustomLists;

use App\Models\CustomList;
use App\Models\CustomListItem;
use Illuminate\Foundation\Http\FormRequest;

class CustomListDeleteItemRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $customListItem = CustomListItem::where('id', request()->route("id"))->first();
        $customList = CustomList::where('id', $customListItem->custom_list_id)->where('user_id', auth()->user()->id)->first();
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
            //
        ];
    }
}
