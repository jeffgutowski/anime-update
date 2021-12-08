<?php

namespace App\Http\Requests\CustomLists;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\CustomList;

class CustomListShowUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $customList = CustomList::where('id', request()->route("id"))
                        ->where('user_id', auth()->user()->id)->first();
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
