<?php

namespace App\Http\Controllers;

use App\Models\CustomList;
use App\Models\CustomListItem;
use Illuminate\Http\Request;
use App\Http\Requests\CustomLists\CustomListDeleteItemRequest;
use App\Http\Requests\CustomLists\CustomListShowUpdateRequest;
use App\Http\Requests\CustomLists\CustomListSaveRequest;
use App\Http\Requests\CustomLists\CustomListItemRequest;
use App\Http\Requests\CustomLists\CustomListDeleteRequest;

class CustomListController extends Controller
{

    public function index()
    {
        return view('frontend.custom_lists.index', [
            'customLists' => CustomList::where('user_id', auth()->user()->id)->orderBy('order_number', 'asc')->paginate(36)
        ]);
    }

    public function arrange()
    {
        return view('frontend.custom_lists.arrange', [
            'custom_lists' => CustomList::where('user_id', auth()->user()->id)->orderBy('order_number', 'asc')->get()
        ]);
    }

    public function message()
    {
        if (request()->has('message')) {
            \Alert::success(request()->get('message'))->flash();
        }
        return redirect("/user/".auth()->user()->name()."?frag=customlists#customlists");
    }

    public function show(Request $request, $id)
    {
        $list = CustomList::where('id', $id)->with('user')->first();

        if (isset($list->public) && ($list->public || (auth()->user() && auth()->user()->id == $list->user_id))) {
            // Do a second query to paginate list items
            $items = CustomListItem::where('custom_list_id', $list->id)->with('product.platform')->orderBy('order_number', $list->order_by)->paginate(100);
            if (!auth()->user() || auth()->user()->id != $list->user_id) {
                $list->increment('clicks');
            }
            return view('frontend.custom_lists.show', ['list' => $list, 'items' => $items]);
        }
        abort(404);
    }

    public function new()
    {
        return view('frontend.custom_lists.form', ['platforms' => []]);
    }

    public function edit(CustomListShowUpdateRequest $request, $id)
    {
        $customList = CustomList::where('id', $id)->first();
        $customList->load(['items' => function($query) use ($customList) {
            $query->orderBy('order_number', 'asc')->with('platform');
        }])->first();

        $platforms = \Cache::remember('platforms:'.session('region.code'), config('cache.timeout.lg'), function() {
            return \App\Models\Platform::orderBy('name', 'asc')->where(session('region.code'), true)->get();
        });

        return view('frontend.custom_lists.form', ['customList' => $customList, 'items' => $customList->items, 'platforms' => $platforms]);
    }

    public function save(CustomListSaveRequest $request)
    {
        $saveRequest = $request->only(['id', 'title', 'youtube_id', 'description', 'public', 'show_order_number', 'order_by', 'custom_item_thumbnails']);
        $saveRequest['user_id'] = auth()->user()->id;
        $saveRequest['youtube_id'] = !empty($saveRequest['youtube_id']) ? $saveRequest['youtube_id'] : null;
        $customList = CustomList::updateOrCreate(['id' => request()->get('id')], $saveRequest);
        if ($request->has('thumbnail') && !empty($request->thumbnail)) {
            $customList->thumbnail = saveS3(\Image::make($request->file('thumbnail')), 'custom-lists/thumbnails', $request->file('thumbnail')->extension(), $customList->thumbnail);
            $customList->save();
        }
        return response()->json($customList);
    }

    public function updateListOrder(Request $request)
    {
        $list = CustomList::where('id', $request->get('id'))->first();
        if ($list->user_id != auth()->id()) {
            abort(401);
        }
        $list->order_number = $request->get('order_number');
        $list->save();
        return response()->json([$request->all(), $list]);
    }

    public function deleteListItem(CustomListDeleteItemRequest $request, $id)
    {
        return CustomListItem::where("id", $id)->delete();
    }

    public function delete(CustomListDeleteRequest $request, $id)
    {
        return CustomList::where("id", $id)->delete();
    }

    public function attachListItem(CustomListItemRequest $request)
    {
        $customList = CustomList::find($request->get('custom_list_id'));
        $customList->items()->attach($request->get('game_id'));
        $item = $customList->items()->get()->last();
        // timestamps for pivots don't work correctly with laravel
        $item->pivot->created_at = date("Y-m-d h:i:s");
        $item->pivot->updated_at = date("Y-m-d h:i:s");
        $item->pivot->order_number = $request->get('order_number');
        $item->pivot->save();
        $customList->updated_at = date("Y-m-d h:i:s");
        $customList->save();
        return response()->json($item);
    }

    public function saveListItem(CustomListItemRequest $request)
    {
        $listItem = CustomListItem::find($request->get('custom_list_item_id'));
        $listItem->description = $request->get('description');
        $listItem->order_number = $request->get('order_number');
        if ($request->has('thumbnail')) {
            $listItem->thumbnail = saveS3(\Image::make($request->file('thumbnail')), 'custom-lists/items/thumbnails', $request->file('thumbnail')->extension(), $listItem->thumbnail);
        }
        $listItem->updated_at = date("Y-m-d h:i:s");
        $listItem->save();
        $listItem->list->updated_at = date("Y-m-d h:i:s");
        $listItem->list->save();
        return response()->json($listItem);
    }
}
