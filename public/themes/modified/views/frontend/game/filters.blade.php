{{-- Start modal for filter options --}}
<div class="modal fade modal-fade-in-scale-up modal-dark" id="modal_filter" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

            <div class="modal-header">

                <div class="background-pattern" style="background-image: url('{{ asset('/img/game_pattern.png') }}');"></div>

                <div class="title">
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">×</span><span class="sr-only">{{ trans('general.close') }}</span>
                    </button>
                    {{-- Title (Filter) --}}
                    <h4 class="modal-title" id="myModalLabel">
                        <i class="fa fa-filter" aria-hidden="true"></i>
                        {{ trans('general.sortfilter.filter') }}
                    </h4>
                </div>

            </div>
            <div class="filter-container">
                <div class="modal-seperator filter-header">
                    {{ trans('games.filters.search_terms') }}
                    <span id="search-count">
                        @if(request()->input('q'))
                            (1)
                        @endif
                    </span>
                    <i class="fas fa-plus float-right"></i>
                </div>
                <div class="modal-body filter-body">
                    <div class="filter-box">
                        <input id="search-terms" class="text-input" type="text" placeholder="Type in search terms" value="{{request()->input('q')}}">
                    </div>
                </div>
            </div>
            {{-- Start platform filters --}}
            <div class="filter-container">
                <div class="modal-seperator filter-header">
                    {{ trans('games.filters.platforms') }}
                    <span id="platform-count">
                        @if(request()->has('p'))
                            ({{count(explode(',', request()->get('p')))}})
                        @endif
                    </span>
                    <i class="fas fa-plus float-right"></i>
                </div>
                <div class="modal-body filter-body">
                    <div class="filter-box">
                        @foreach($platforms as $platform)
                            <div class="filter-item">
                                <input class="platform-checkbox filter-checkbox" type="checkbox" id="platform-{{$platform->id}}" name="platform-{{$platform->id}}" value="{{$platform->id}}" {{(in_array($platform->id, explode(',', request()->get('p'))))?"checked":""}}>
                                <label class="filter-label" for="platform-{{$platform->id}}">{{$platform->name}}</label>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            {{-- Ratings Filter --}}
            <div class="filter-container" hidden> {{-- Ratings Currently Hidden to Reduce Clutter on the Site --}}
                <div class="modal-seperator filter-header">
                    {{ trans('games.filters.ratings') }}
                    <span id="ratings-count">
                        @if(request()->has("r"))
                            (1)
                        @endif
                    </span>
                    <i class="fas fa-plus float-right"></i>
                </div>
                <div class="modal-body filter-body">
                    <div>
                        <input type="radio" id="rating-none" class="star-radio" name="star-rating" value="" {{ request()->has("r") ? "" : "checked" }}>
                        <label for="rating-none" class="text-label">{{ trans('games.filters.no_ratings_filter') }}</label>
                    </div>
                    {{-- Create Radio buttons for star ratings desc from 4 to 1--}}
                    @for($i = 4; $i > 0; $i--)
                        <div>
                            <input type="radio" id="rating-{{$i}}" class="star-radio" name="star-rating" value="{{$i}}" {{ request()->get("r") == $i ? "checked" : "" }}>
                            <label for="rating-{{$i}}"><span class="star-label" id="star-{{$i}}"></span><b> & </b><i class="fas fa-arrow-up"></i></label>
                        </div>
                    @endfor
                    <div>
                        <input type="radio" id="rating-custom" class="star-custom" name="star-rating" value="custom" {{ request()->get("r") == "custom" ? "checked" : "" }}>
                        <label for="rating-custom" class="text-label">{{ trans('games.filters.custom') }}</label>
                    </div>
                    <div class="rating-range {{ request()->get("r") == "custom" ? "" : "hidden" }}">
                        <input class="text-input" id="rating-min" type="number" step="0.1" min="0" max="5" placeholder="Min" value="{{substr(request()->get("rc"), 0, strpos(request()->get("rc"), "-"))}}">
                        -
                        <input class="text-input" id="rating-max" type="number" step="0.1" min=".1" max="5" placeholder="Max" value="{{substr(request()->get("rc"), strpos(request()->get("rc"), "-") + 1)}}">
                    </div>
                </div>
            </div>
            @if(request()->segment(1) != "games")
                {{-- Product Types Filter--}}
                <div class="filter-container">
                    <div class="modal-seperator filter-header">
                        {{ trans('games.filters.types') }}
                        <span id="types-count">
                            @if(request()->has('t'))
                                ({{count(explode(',', request()->get('t')))}})
                            @endif
                            </span>
                        <i class="fas fa-plus float-right"></i>
                    </div>
                    <div class="modal-body filter-body">
                        <div class="filter-box">
                            @if(request()->segment(1) != "hardware")
                                <div class="filter-item">
                                    <input class="types-checkbox filter-checkbox" type="checkbox" id="type-game" name="type-game" value="game" {{(in_array('game', explode(',', request()->get('t'))))?"checked":""}}>
                                    <label class="filter-label" for="type-game">Game</label>
                                </div>
                            @endif
                            @foreach($product_types as $type)
                                <div class="filter-item">
                                    <input class="types-checkbox filter-checkbox" type="checkbox" id="type-{{$type->slug}}" name="type-{{$type->slug}}" value="{{$type->slug}}" {{(in_array($type->slug, explode(',', request()->get('t'))))?"checked":""}}>
                                    <label class="filter-label" for="type-{{$type->slug}}">{{$type->name}}</label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
            @if(request()->segment(1) == "listings" || request()->segment(1) == 'user' && (in_array(request()->get('frag'), ['listings', ''])))
                    <div class="filter-divider">Listings Filters</div>
                    {{-- Price Range --}}
                    <div class="filter-container">
                        <div class="modal-seperator filter-header">
                            {{ trans('games.filters.price_range') }}
                            @if(request()->has("pr"))
                            <span id="condition-count">
                                (1)
                            </span>
                            @endif
                            <i class="fas fa-plus float-right"></i>
                        </div>
                        <div class="modal-body filter-body">
                            <div class="filter-box">
                                <div class="filter-item">
                                    $<input class="text-input" id="price-min" type="number" step="0.01" placeholder="Min" value="{{substr(request()->get("pr"), 0, strpos(request()->get("pr"), "-"))}}">
                                    <span style="margin-left: 5px; margin-right: 5px;">-</span>
                                    $<input class="text-input" id="price-max" type="number" step="0.01" placeholder="Max" value="{{substr(request()->get("pr"), strpos(request()->get("pr"), "-") + 1)}}">
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- Condition Filter --}}
                    <div class="filter-container">
                        <div class="modal-seperator filter-header">
                            {{ trans('games.filters.conditions') }}
                            <span id="condition-count">
                                @if(request()->has('co'))
                                    ({{count(explode(',', request()->get('co')))}})
                                @endif
                            </span>
                            <i class="fas fa-plus float-right"></i>
                        </div>
                        <div class="modal-body filter-body">
                            <div class="filter-box">
                                @for($i = 9; $i--; $i >= 0)
                                    <div class="filter-item">
                                        <input type="checkbox" id="condition-{{$i}}" class="condition-filter" value="{{$i}}" {{( request()->has('co') && in_array($i, explode(',', request()->get('co')))) !== false ? "checked" : ""}}>
                                        <label for="condition-{{$i}}" class="condition-label">{{ trans("listings.general.conditions.$i") }}</label>
                                    </div>
                                @endfor
                            </div>
                        </div>
                    </div>
            @endif
            @if(request()->segment(1) !== "hardware" && request("frag") != "hardware")
                <div class="filter-divider">Games Filters</div>
                {{-- Genres Filter --}}
                <div class="filter-container">
                    <div class="modal-seperator filter-header">
                        {{ trans('games.filters.genres') }}
                        <span id="genre-count">
                            @if(!empty($filters->genresIncluded) || !empty($filters->genresExcluded))
                                    ({{count($filters->genresIncluded) + count($filters->genresExcluded)}})
                            @endif
                        </span>
                        <i class="fas fa-plus float-right"></i>
                    </div>
                    <div class="modal-body filter-body">
                        <div>Exclusive = Games will have at least one of these selected genres</div>
                        <div>Inclusive = Games will have all selected genres</div>
                        <div style="padding-bottom: 5px;"></div>
                        <div>
                            <span class="check3s positive fa fa-check fa1 disabled" name=""></span>
                            <label class="check3s-label"> =
                                <select id="genre-exclusive" class="dropdown-select">
                                    <option value="e">Exclusive</option>
                                    <option value="i" {{(app('request')->input('ge') === "i") ? "selected" : null }}>Inclusive</option>
                                </select>
                            </label>
                        </div>
                        <div>
                            <span class="check3s negative fa fa-times fa1 disabled" name="" disabled></span>
                            <label class="check3s-label">= Games with these genres will be filtered out</label>
                        </div>
                        <hr>
                        <div class="filter-box">
                            @foreach($genres as $genre)
                                <div class="filter-item">
                                    <span class="genre-checkbox check3s {{(in_array($genre->id, $filters->genresIncluded) ? 'positive fa fa-check fa1': '')}} {{(in_array($genre->id, $filters->genresExcluded) ? 'negative fa fa-times fa1': null)}}" name="genre-{{$genre->id}}" data-value="{{$genre->id}}"></span>
                                    <label class="check3s-label" for="genre-{{$genre->id}}">{{$genre->name}}</label>
                                </div>
                            @endforeach
                        </div>

                    </div>
                </div>

                {{-- ESRB Filter--}}
                @if(in_array(session('region.code'), ["ntsc_u", "pal"]))
                    <div class="filter-container">
                        <div class="modal-seperator filter-header">
                            @if(session('region.code') == 'ntsc_u')
                                ESRB Ratings
                            @elseif(session('region.code') == 'pal')
                                PEGI Ratings
                            @endif
                            <span id="genre-count">
                                @if(count(array_filter(explode(',', request('a')))) > 0)
                                    ({{ count(array_filter(explode(',', request('a'))))  }})
                                @endif
                            </span>
                            <i class="fas fa-plus float-right"></i>
                        </div>
                        <div class="modal-body filter-body">
                            <div class="age-filter">
                            @php
                                $ratings = [];
                                if (session('region.code') == 'ntsc_u') {
                                    $ratings = config('age-ratings.esrb');
                                } elseif (session('region.code') == 'pal') {
                                    $ratings = config('age-ratings.pegi');
                                }
                            @endphp
                            @foreach($ratings as $rating => $url)
                                <label class="age-filter-box">
                                    <input class="age-filter-checkbox" type="checkbox" value="{{$rating}}" {{in_array($rating, explode(',', request('a'))) ? "checked" : ""}}>
                                    <img class="age-filter-img" src="{{$url}}">
                                </label>
                            @endforeach
                            @can('access_backend')
                                <label class="age-filter-box">
                                    <input class="age-filter-checkbox" type="checkbox" value="unrated" {{in_array("unrated", explode(',', request('a'))) ? "checked" : ""}}>
                                    <img class="age-filter-img" src="https://game-seeker.s3.amazonaws.com/ratings/U.png">
                                </label>
                            @endcan
                            </div>
                        </div>
                    </div>
                @endif
                {{-- Years Filter --}}
                <div class="filter-container">
                    <div class="modal-seperator filter-header">
                        {{ trans('games.filters.release_year') }}
                        <span id="genre-count">
                            @if((int)(!is_null(request('ys'))) + (int)(!is_null(request('ye'))) > 0)
                                ({{ (int)(!is_null(request('ys'))) + (int)(!is_null(request('ye'))) }})
                            @endif
                        </span>
                        <i class="fas fa-plus float-right"></i>
                    </div>
                    <div class="modal-body filter-body">
                        <label for="release_year_start" class="year-input-label">Start</label>
                        <select id="release_year_start" class="dropdown-select dropdown-year">
                            <option value=""></option>
                            @for($year = 1977; $year <= (int) date('Y') + 2; $year++)
                                <option value="{{$year}}" {{ request('ys') == $year ? "selected" : "" }}>
                                    {{$year}}
                                </option>
                            @endfor
                        </select>
                        <label for="release_year_end" class="year-input-label">End</label>
                        <select id="release_year_end" class="dropdown-select dropdown-year">
                            <option value=""></option>
                            @for($year = 1977; $year <= (int) date('Y') + 2; $year++)
                                <option value="{{$year}}" {{ request('ye') == $year ? "selected" : "" }}>
                                    {{$year}}
                                </option>
                            @endfor
                        </select>
                    </div>
                </div>
                {{-- Developers Filter --}}
                <div class="filter-container">
                    <div class="modal-seperator filter-header">
                        {{ trans('games.filters.developers') }}
                        <span id="developer-count">
                  @if(!empty($filters->developers))
                                ({{count($filters->developers)}})
                            @endif
                  </span>
                        <i class="fas fa-plus float-right"></i>
                    </div>
                    <div class="modal-body filter-body">
                        <input class="text-input" name="developer-search" type='text' id='developer-search' list='developers' placeholder="Search for Developers" >
                        <datalist id="developers"></datalist>
                        <span class="selected-list" id="selected-developers">
                      @foreach($filters->developers as $developer)
                                <span onclick="removeFilter(this, 'd', {{$developer->id}})" data-value="{{$developer->id}}" class="clickable label platform-label platform-filter m-r-5 m-b-5 inline-block platform-filter-active"><i class="fas fa-times-circle search-select"></i>{{$developer->name}}</span>
                            @endforeach
                  </span>
                    </div>
                </div>
                {{-- Publishers Filter --}}
                <div class="filter-container">
                    <div class="modal-seperator filter-header">
                        {{ trans('games.filters.publishers') }}
                        <span id="publisher-count">
                  @if(!empty($filters->publishers))
                                ({{count($filters->publishers)}})
                            @endif
                  </span>
                        <i class="fas fa-plus float-right"></i>
                    </div>
                    <div class="modal-body filter-body">
                        <input class="text-input" name="publisher-search" type='text' id='publisher-search' list='publishers' placeholder="Search for Publishers" >
                        <datalist id="publishers"></datalist>
                        <span class="selected-list" id="selected-publishers">
                  @foreach($filters->publishers as $publisher)
                                <span onclick="removeFilter(this, 'pu', {{$publisher->id}})" data-value="{{$publisher->id}}" class="clickable label platform-label platform-filter m-r-5 m-b-5 inline-block platform-filter-active"><i class="fas fa-times-circle search-select"></i>{{$publisher->name}}</span>
                            @endforeach
              </span>
                    </div>
                </div>
            @endif
            @if(request()->segment(1) != "games" && (request()->get('frag') != 'completedlist' && request()->get('frag') != 'collection'))
                @if(request()->segment(1) != "games")
                    <div class="filter-divider">Hardware & Accessories Filters</div>
                @endif
                {{-- Companies/Manufacturer Filter--}}
                <div class="filter-container">
                    <div class="modal-seperator filter-header">
                        {{ trans('games.filters.manufacturers') }}
                        <span id="publisher-count">
              @if(!empty($filters->companies))
                                ({{count($filters->companies)}})
                            @endif
              </span>
                        <i class="fas fa-plus float-right"></i>
                    </div>
                    <div class="modal-body filter-body">
                        <input class="text-input" name="company-search" type='text' id='company-search' list='companies' placeholder="Search for Manufacturers" >
                        <datalist id="companies"></datalist>
                        <span class="selected-list" id="selected-companies">
              @foreach($filters->companies as $company)
                                <span onclick="removeFilter(this, 'c', {{$company->id}})" data-value="{{$company->id}}" class="clickable label platform-label platform-filter m-r-5 m-b-5 inline-block platform-filter-active"><i class="fas fa-times-circle search-select"></i>{{$company->name}}</span>
                            @endforeach
                  </span>
                    </div>
                </div>
            @endif
            <div class="modal-footer">
                {{-- Cancel button --}}
                <a href="#" data-dismiss="modal" data-bjax class="btn btn-dark btn-animate btn-animate-vertical">
                    <span><i class="icon fa fa-times" aria-hidden="true"></i> {{ trans('general.cancel') }}</span>
                </a>
                {{-- Filter submit button --}}
                <a class="btn btn-success btn-animate btn-animate-vertical" id="filter-submit" href="#">
            <span>
              <i class="icon fa fa-filter" aria-hidden="true"></i> {{ trans('general.sortfilter.filter') }}
            </span>
                </a>
            </div>
        </div>
    </div>
</div>
{{-- End modal for filter options --}}
