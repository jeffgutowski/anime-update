<div>
    <div class="panels-title border-bottom flex-center-space">
        {{-- Title --}}
        <div>
            @if(request()->segment(1) === "games")
                <i class="fa fa-gamepad" aria-hidden="true"></i> {{ trans('games.overview.games') }}
            @elseif(request()->segment(1) === "hardware")
                <i class="fa fa-hdd" aria-hidden="true"></i> {{ trans('games.overview.hardware') }}
            @elseif(request()->segment(1) === "user" && request("frag") == 'wishlist')
                <i class="fa fa-heart" aria-hidden="true"></i> {{ trans('wishlist.wishlist') }}
            @elseif(request()->segment(1) === "dash" && request()->segment(2) === "quick-shop")
                <i class="fa fa-store-alt" aria-hidden="true"></i> Quick Shop
            @elseif(request()->segment(1) === "listings" || request('frag') == "listings" || !request()->has("frag") && request()->segment(1) === "user")
                <i class="fa fa-tags" aria-hidden="true"></i> {{ trans('general.listings') }}
            @elseif(request()->segment(1) === "user" && request("frag") == 'collection')
                <i class="fa fa-clipboard-list" aria-hidden="true"></i>  Collection
            @elseif(request()->segment(1) === "user" && request("frag") == 'hardware')
                <i class="fa fa-hdd" aria-hidden="true"></i>  Hardware Collection
            @elseif(request()->segment(1) === "user" && request("frag") == 'completedlist')
                <i class="fa fa-clipboard-check" aria-hidden="true"></i><span>
                    @if(isset($user) && auth()->check() && auth()->user()->id == $user->id)
                        Games I've Completed
                    @else
                        Completed Games
                    @endif
                </span>
            @endif
        </div>
        @if(isset($pagination_obj) && method_exists($pagination_obj, 'total') || true)
            <div>Total: {{ number_format($pagination_obj->total(), 0, ".", ",")}}</div>
        @endif
        {{-- Current page + page count --}}
        <div class="o-50">
            {{-- First check if pages exist --}}
            @if(!is_null($pagination_obj) && $pagination_obj->lastPage())
                <span id="current-page">Page: {{ $pagination_obj->currentPage() }}</span> / <span id="last-page">{{ $pagination_obj->lastPage() }}</span>
            @endif
        </div>
    </div>

    <div class="m-b-20 flex-center-space">
        {{-- Start Filter button --}}
        <div>
            @if(request()->segment(1) == "user" && in_array(request('frag'), ['collection', 'hardware', 'listings', null]))
                @include('frontend.layouts.inc.regionsFilter')
            @endif
            {{-- Filter Button with active filter count - open modal --}}
            <a href="#" data-toggle="modal" data-target="#modal_filter" class="btn btn-dark">
                <i class="fa fa-filter" aria-hidden="true"></i> {{ trans('general.sortfilter.filter') }} @if($filters->filterCount > 0) ({{$filters->filterCount}}) @endif
            </a>
            {{-- Remove button - only visible with active filters --}}
            @if($filters->filterCount > 0 || false)
                <a id="remove-filter" href="{{parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH)}}" class="m-l-5 btn btn-dark">
                    <i class="fa fa-times" aria-hidden="true"></i>
                </a>
            @endif
        </div>
        {{-- End Filter button --}}
        {{-- Start sort options --}}
        <div class="sort-filter">
            @if(auth()->check())
                <div class="ratings-switch">
                    <div class="site-ratings-btn ratings-btn {{ in_array(request('rs'), ['site', null]) ? 'ratings-btn-selected' : '' }}" data-value="site">Site Ratings</div>
                    <div class="my-ratings-btn ratings-btn {{ request('rs') == 'my' ? 'ratings-btn-selected' : '' }}" data-value="my">My Ratings</div>
                </div>
            @endif
            {{-- Sort order button (desc / asc) --}}
            <a id="order-direction-btn" class="btn btn-dark" style="vertical-align: inherit;">
                <i id="order-direction" class="fa fa-sort-amount-{{ $filters->direction == 'desc' ? 'down' : 'up' }}" aria-hidden="true"></i>
            </a>
            {{-- Sort dropdown --}}
            <div class="m-l-5 inline-block">
                <select id="order_by" class="form-control select" style="height: 33px !important;">
                    @if((request()->segment(1) == 'dash' && request()->segment(2) == 'quick-shop') || request()->segment(1) == 'listings')
                        {{-- Date --}}
                        <option value="-listings.created_at" {{ strpos(request()->input('o'), 'listings.created_at') !== false ? 'selected' : ''}}>{{ trans('general.sortfilter.listing_date') }}</option>
                    @endif
                    {{-- Name --}}
                    <option value="name" {{ strpos(request()->input('o'), 'name') !== false ? 'selected' : ''}}>{{ trans('general.sortfilter.name') }}</option>
                    @if((request()->segment(1) == 'dash' && request()->segment(2) == 'wishlist') || request()->segment(1) == 'listings')
                        {{-- Price --}}
                        <option value="price" {{ strpos(request()->input('o'), 'price') !== false ? 'selected' : ''}}>{{ trans('general.sortfilter.sort_price') }}</option>
                    @endif
                    {{-- Release option --}}
                    <option value="-release" {{ strpos(request()->input('o'), 'release') !== false ? 'selected' : ''}}>{{ trans('general.sortfilter.sort_release') }}</option>
                    {{-- Rating option --}}
                    <option value="{{request('rs') == 'my' ? '-rating': '-average_rating'}}" {{ in_array(request()->input('o'), ["average_rating", "-average_rating", 'rating', '-rating']) ? 'selected' : '' }}>{{ trans('general.sortfilter.rating') }}</option>
                    <option value="{{request('rs') == 'my' ? '-difficulty': '-average_difficulty'}}" {{ in_array(request()->input('o'), ["average_difficulty", "-average_difficulty", 'difficulty', '-difficulty']) ? 'selected' : ''}}>{{ trans('general.sortfilter.difficulty') }}</option>
                    <option value="{{request('rs') == 'my' ? '-duration': '-average_duration'}}" {{ in_array(request()->input('o'), ["average_duration", "-average_duration", 'duration', '-duration']) ? 'selected' : '' }}>{{ trans('general.sortfilter.duration') }}</option>
                    {{-- Relevance --}}
                    @if(request()->input('q', false))
                        <option value="-rank" {{ strpos(request()->input('o'), 'rank') !== false ? 'selected' : '' }}>Relevance</option>
                    @endif
                </select>
            </div>
        </div>
    </div>
</div>
