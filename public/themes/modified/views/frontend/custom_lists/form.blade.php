@extends(Theme::getLayout())

@section('subheader')
    {{-- Start Subheader --}}
    <div class="subheader">

        <div class="background-pattern" style="background-image: url('{{ asset('/img/game_pattern.png') }}') !important;"></div>
        <div class="background-color"></div>

        <div class="content">
            <span class="title"><i class="fa fa-list-ol"></i> Lists</span>
        </div>

    </div>
    {{-- End Subheader --}}
@stop

@section('content')
    <div style="width: 100%">
        <table style="width: 100%">
            <tr>
                <td>
                    <a href="/user/{{auth()->user()->name}}?frag=customlists#customlists"><button class="btn btn-primary">My Lists</button></a>
                </td>
                <td style="text-align: center">
                    <a href="/custom-lists/new"><button class="btn btn-success"><i class="fa fa-plus"></i> New List</button></a>
                </td>
                <td style="text-align: right">
                    <a href="/custom-lists/{{isset($customList) ? $customList->id : null}}"><button id="view-btn" class="btn btn-primary" {{isset($customList) ? null : 'disabled'}}><i class="fa fa-arrow-left"></i> Back to List</button></a>
                </td>
            </tr>
        </table>
    </div>

    <br/>
    <div class="listing-form">
        <div class="panel">
            <div class="panel-heading" style="min-height: 70px">
                <h3 class="panel-title"><i class="fa fa-pencil m-r-5"></i>
                    @if(request()->segment(2) == 'new')
                        Create List
                    @elseif(request()->segment(2) == 'edit')
                        Edit List
                    @endif
                    <span style="float:right; margin-right: 20px">
                        <span id="save-indicator"></span>
                        @if(isset($customList) ? false : true)
                            <button style="height: 30px" id="save-list" class="btn btn-success" disabled>Create List</button>
                        @endif
                    </span>
                    <span style="top:20px" class="delete" onclick="deleteList(this)">
                        <i class="fa fa-trash"></i>
                    </span>
                </h3>
            </div>
            <div class="panel-body">
                <div class="custom-list">
                    <input type="hidden" id="id" name="id" class="custom_list" value="{{isset($customList) ? $customList->id : null}}">
                    <div class="input-wrapper">
                        <div class="input-header">Title</div>
                        <input id="title" name="title" class="dark-input full-width custom_list" type="text" value="{{isset($customList) ? $customList->title : null}}">
                    </div>
                    <div>
                        <input class="custom_list" type="radio" id="image-only" name="image" value="thumbnail" {{ isset($customList) && $customList->youtube_id ? null : 'checked'}}>
                        <label class="radio-label" for="image-only">Image</label>
                        <input class="custom_list" type="radio" id="youtube" name="image" value="youtube_id" {{ isset($customList) && $customList->youtube_id ? 'checked' : null}}>
                        <label class="radio-label" for="youtube">Youtube Video</label>
                    </div>
                    <div class="input-wrapper {{ isset($customList) && $customList->youtube_id ? 'hidden' : null }}" id="thumbnail-input">
                        <div class="input-header">Thumbnail</div>
                        <input class="custom_list" id="thumbnail" name="thumbnail" class="dark-input" type="file" onchange="readImage(this)">
                        <div>
                            <img class="custom-cover" src="{{isset($customList) ? $customList->thumbnail : null}}">
                        </div>
                    </div>
                    <div class="input-wrapper {{ isset($customList) && $customList->youtube_id ? '' : 'hidden' }}" id="youtube-input" >
                        <div class="input-header">Youtube Video ID</div>
                        <input class="custom_list dark-input" id="youtube_id" name="youtube_id" class="dark-input full-width" type="text" value="{{ isset($customList) ? $customList->youtube_id : null}}">
                    </div>
                    <div class="input-wrapper">
                        <div class="input-header">Description</div>
                        <div class="flex-center">
                            <textarea id="description" class="form-control input custom_list" name="description">{{isset($customList) ? $customList->description : null}}</textarea>
                        </div>
                    </div>
                    <div>
                        <input class="custom_list" id="public" name="public" type="checkbox" {{isset($customList) && $customList->public ? "checked" : null}}>
                        <label for="public" class="checkbox-label">Make List Public</label>
                        <br/>
                        <input class="custom_list" id="show_order_number" name="show_order_number" type="checkbox" {{isset($customList) && $customList->show_order_number ? "checked" : null}}>
                        <label for="show_order_number" class="checkbox-label">Display Order Numbers</label>
                        <br/>
                        <input class="custom_list" id="order-by" name="order-by" type="checkbox" {{isset($customList) && $customList->order_by == "desc" ? "checked" : null}}>
                        <label for="order-by" class="checkbox-label">Order By Counting Down</label>
                        <br/>
                        <input class="custom_list" id="custom_item_thumbnails" name="custom_item_thumbnails" type="checkbox" {{isset($customList) && $customList->custom_item_thumbnails ? "checked" : null}}>
                        <label for="custom_item_thumbnails" class="checkbox-label">Use Custom Thumbnails Within List</label>
                    </div>
                </div>
                <br/>
                <ul id="list-items"></ul>
                <li id="list-item" class="list-content hidden">
                    <i class="fa fa-chevron-up drag-icon" style="position: relative; left: calc(50%); float:left;"></i>
                    <input class="item_id" type="hidden">
                    <span class="delete" onclick="deleteItem(this)">
                        <span class="saving" style="margin-right: 5px"></span>
                        <i class="fa fa-trash"></i>
                    </span>
                        <div class="item-image">
                            <div class="platform-background create">
                                <img class="platform-cover list-item-platform {{isset($customList) && $customList->custom_item_thumbnails ? 'hidden' : ''}}" src="">
                            </div>
                            <img class="item-cover {{isset($customList) && $customList->custom_item_thumbnails ? "hidden" : ""}}" src="">
                            <div class="input-wrapper item-thumbnail {{isset($customList) && $customList->custom_item_thumbnails ? "" : "hidden"}}">
                                <div class="input-header">Thumbnail</div>
                                <input name="custom_thumbnail" class="dark-input custom_thumbnail" type="file" onchange="readImage(this)" hidden>
                                <input type="button" value="Choose File" onclick="$(this).parent().find('.custom_thumbnail').click()" />
                                <div>
                                    <img class="custom-cover" src="">
                                </div>
                            </div>
                        </div>
                        <div class="item-details" style="">
                            <div class="input-wrapper">
                                <span class="input-header">Order Number </span>
                                <input type="number" min="1" class="dark-input order_number">
                            </div>
                            <div class="input-wrapper">
                                <h3 class="item-name"></h3>
                            </div>
                            <div class="input-wrapper">
                                <div class="input-header">Description</div>
                                <div class="flex-center">
                                    <textarea class="save-list-item form-control input description"></textarea>
                                </div>
                            </div>
                        </div>
                    <i class="fa fa-chevron-down drag-icon" style="position: relative; left: calc(50%); bottom:5px; float:left;"></i>
                </li>
                <div id="list-item-section" class="{{!isset($customList) ? 'hidden' : null}}">
                    <label style="margin-right: 5px; margin-top:10px">Search Products</label>
                    <input id="search-product" class="dark-input" type="text" style="margin-right: 10px; margin-bottom: 5px;">
                    <span class="platform-search-filter">
                    <label style="margin-right: 5px; margin-bottomp:5px">Platform</label>
                    <select id="platform-filter" class="dark-input" style="height: 23px;">
                        <option value="">All</option>
                        @foreach($platforms as $platform)
                            <option value="{{$platform->id}}">{{$platform->name}}</option>
                        @endforeach
                    </select>
                    </span>
                    <span id="search-btn" class="btn btn-primary float-right" style="margin-bottom: 5px">Load</span>


                    <div id="search-result" class="hidden search-result" style="width: 200px">
                        <input type="hidden" class="product-index">
                        <div class="btn btn-success add-btn" onclick="addItem(this)">+Add</div>
                        <div class="product-container">
                            <div class="platform-background">
                                <img class="platform-cover" src="">
                            </div>
                            <img class="product-cover" src="" style="max-width:200px;">
                            <div class="product-name">Name</div>
                        </div>
                    </div>

                    <div id="products-list" class="load-list" style="">
                        No search results
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('after-scripts')
    <script type="text/javascript">
        function readImage(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    $(input).parent().find('.custom-cover').attr('src', e.target.result).css('max-width', '200px')
                };
                reader.readAsDataURL(input.files[0]);
            }
            window.saveListItem($(input).parents("li"))
        }
        function deleteItem(item) {
            fields = $(item).parent()
            let confirmation = confirm("Are you sure you want to delete "+fields.find('.item-name').text()+" from the list?")
            if (confirmation) {
                $.ajax({
                    url: "/custom-lists/items/" + fields.find('.item_id').val(),
                    headers: { 'X-CSRF-TOKEN': Laravel.csrfToken },
                    type: 'DELETE',
                    success: function (response) {
                        $(item).closest(".list-content").slideUp()
                        $(item).destroy()
                    }
                });
            }
        }
        function deleteList(list) {
            let confirmation = confirm("Are you sure you want to delete this list?")
            if (confirmation) {
                $.ajax({
                    url: "/custom-lists/" + $("#id").val(),
                    headers: { 'X-CSRF-TOKEN': Laravel.csrfToken },
                    type: 'DELETE',
                    success: function (response) {
                        window.location = "/custom-lists/message?message=List Successfully Deleted"
                    }
                });
            }
        }
        function addItem(item) {
            productIndex = $(item).parent().find(".product-index").val();
            for (i = 0; i < window.products.length; i++) {
                if (i == productIndex) {
                    $.ajax({
                        url: "/custom-lists/items/attach",
                        data: {custom_list_id: $("#id").val(), game_id: window.products[productIndex].id, order_number: $('#list-items').find('li').length + 1},
                        headers: { 'X-CSRF-TOKEN': Laravel.csrfToken },
                        type: 'POST',
                        success: function (response) {
                            clone = $("#list-item").clone()
                            clone.attr("id", null)
                            clone.find(".order_number").val($('#list-items').find('li').length + 1)
                            clone.find(".item_id").val(response.pivot.id)

                            clone.find(".item-name").text(window.products[productIndex].name)
                            clone.find(".platform-cover")
                                .attr('src', window.products[productIndex].platform.cover_image)
                            clone.find(".platform-background").css('background', window.products[productIndex].platform.color).css("text-align", window.products[productIndex].platform.cover_position)
                            clone.find(".item-cover").attr("src", window.products[productIndex].cover_{{session('region.abbr')}})
                            $("#list-items").append(clone)
                            clone.slideDown()
                        }
                    });
                }
            }
        }

        $(document).ready(function() {
            window.products = {};
            // when updating show list items
            var items = {!! isset($customList) ? json_encode($customList->items->toArray(), JSON_HEX_TAG) : "{}" !!};
            for (i = 0; i < items.length; i++) {
                clone = $("#list-item").clone()
                clone.attr("id", null)
                clone.find(".item_id").val(items[i].pivot.id)
                clone.find(".item-name").text(items[i].name)
                clone.find(".platform-cover")
                    .attr('src', items[i].platform.cover_image)
                clone.find(".platform-background").css('background', items[i].platform.color).css("text-align", items[i].platform.cover_position)
                clone.find(".item-cover").attr("src", items[i].cover_{{session('region.abbr')}})
                clone.find(".custom-cover").attr("src", items[i].pivot.thumbnail)
                clone.find(".order_number").val(items[i].pivot.order_number)
                clone.find(".description").val(items[i].pivot.description)
                $("#list-items").append(clone)
                clone.show()
            }
            $('#image-only').change(function () {
                $('#youtube-input').hide();
                $('#thumbnail-input').show();
            })

            $('#youtube').change(function () {
                $('#thumbnail-input').hide();
                $('#youtube-input').show();
            })

            $("#add-btn").click(function () {
                let clone = $("#list-item").clone();
                clone.attr('id', null)
                $("#products-list").append(clone)
                clone.slideDown()
            })

            $("#custom_item_thumbnails").click(function() {
                if ($(this).is(":checked")) {
                    $(".item-cover, .list-item-platform").hide()
                    $(".item-thumbnail").show()
                } else {
                    $(".item-thumbnail").hide()
                    $(".item-cover, .list-item-platform").show()
                }
            })

            function searchProducts() {
                $("#products-list").empty()
                $.ajax({
                    url: "/api/product/search",
                    data: {q: $("#search-product").val(), p: $("#platform-filter").val(), limit: 100},
                    success: function (response) {
                        window.products = response.data
                        for (i = 0; i < response.data.length; i++) {
                            clone = $("#search-result").clone()
                            clone.find(".product-index").val(i)
                            clone.find(".platform-background").css('background', response.data[i].platform.color).css("text-align", response.data[i].platform.cover_position)
                            clone.find("img.platform-cover")
                                .attr("src", response.data[i].platform.cover_image)

                            clone.find("img.product-cover").attr("src", response.data[i].cover_{{session('region.abbr')}})
                            clone.find(".product-name").text(response.data[i].name)
                            clone.css("display", "inline-block")
                            $("#products-list").append(clone)
                        }
                        if (response.data.length == 0) {
                            $("#products-list").text("No search results")
                        }
                    }
                });
            }
            $("#search-btn").on('click', function() {
                searchProducts()
            });
            $("#search-product").on('keyup', function (e) {
                if (e.key === 'Enter' || e.keyCode === 13) {
                    searchProducts()
                }
            });


            $('#save-list').on('click', function() {
                window.saveList()
            })

            window.saveList = function() {
                $("#save-indicator").html('<i class="fa fa-spinner fa-pulse fa-fw"></i>');
                let request = new FormData();
                $('.custom_list').each(function (index, elem) {
                    if($(elem).is(':checkbox')) {
                        request.append($(elem).attr('name'), $(elem).is(':checked') ? 1 : 0)
                    } else if ($(elem).is(':radio')) {
                        if ($(elem).is(':checked')) {
                            request.append($(elem).attr('name'), $(elem).val())
                        }
                    } else {
                        request.append($(elem).attr('name'), $(elem).val())
                    }
                });
                if (request.get('image') == 'thumbnail') {
                    request.append("youtube_id", '')
                    if ($("#thumbnail")[0].files[0]) {
                        request.append("thumbnail", $("#thumbnail")[0].files[0])
                    }
                } else if (request.get('image') == 'youtube_id') {
                    request.append("thumbnail", '')
                }
                request.set("order_by", request.get('order-by') == 1 ? 'desc' : 'asc')

                $.ajax({
                    url: "/custom-lists/save",
                    type: 'POST',
                    contentType: false,
                    processData: false,
                    headers: { 'X-CSRF-TOKEN': Laravel.csrfToken },
                    data: request,
                    success: function(response) {
                        // redirect so when if page is reloaded you are still editing the same custom list
                        if (!$('#id').val()) {
                            window.location = '/custom-lists/edit/' + response.id
                        }
                        setTimeout(function () {
                            $("#save-indicator").html('<i class="fa fa-save"></i>');
                            setTimeout(function () {
                                $("#save-indicator").html('');
                            }, 3000)
                        }, 1000)
                    },
                    error: function(xhr, status, error) {
                        let response = JSON.parse(xhr.responseText)
                        $.each(response.errors, function(key, val) {
                            alert(val)
                            return false; // break loop after first error
                        })
                        setTimeout(function () {
                            $("#save-indicator").html('');
                        }, 3000)
                    }
                });
            };
            $("textarea").each(function(textarea) {
                while($(this).outerHeight() < this.scrollHeight + parseFloat($(this).css("borderTopWidth")) + parseFloat($(this).css("borderBottomWidth"))) {
                    $(this).height($(this).height()+1);
                };
            });

            $("#list-items").sortable({
                opacity: .3,
                cursor: "pointer",
                placeholder: "list-placeholder",
                tolerance: "pointer",
                scroll: true,
                scrollSpeed: 500,
            })
        });
    </script>
    <script src="{{ asset('js/custom-lists.js') }}?v={{ hash_file('md5', base_path().'/public/js/custom-lists.js') }}"></script>
@stop
