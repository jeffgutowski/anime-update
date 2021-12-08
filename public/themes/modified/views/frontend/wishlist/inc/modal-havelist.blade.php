{{-- Start Modal for new messages --}}
<div class="modal fade modal-fade-in-scale-up modal-danger modal-wishlist" id="{{ isset($game->collection[0]) ? 'EditHavelist_' . $game->collection[0]->id : 'AddHavelist' }}" tabindex="-1" role="dialog">
  <div class="modal-dialog user-dialog">
    <div class="modal-content">
      {{-- Modal Header --}}
      <div class="modal-header">
        {{-- Background pattern --}}
        <div class="background-pattern" style="background-image: url('{{ asset('/img/game_pattern.png') }}');"></div>
        {{-- Background color --}}
        <div class="background-color"></div>
        {{-- Modal title --}}
        <div class="title">
          {{-- Close button --}}
          <button type="button" class="close" data-dismiss="modal">
            <span aria-hidden="true">×</span><span class="sr-only">{{ trans('listings.modal.close') }}</span>
          </button>
          {{-- Title --}}
          <h4 class="modal-title">
            <i class="fas fa-clipboard-list m-r-5"></i>
            @if(isset($game->collection))
              {{-- Trans: Update Collection --}}
              <strong>Update Collection</strong>
            @else
              {{-- Trans: Add to Collection --}}
              <strong>Add to Collection</strong>
            @endif
          </h4>
        </div>
      </div>

      {{-- Start selected game panel --}}
      <div class="selected-game flex-center">
        {{-- Game cover --}}
        <div>
          <span class="avatar m-r-10"><img src="{{ $game->image_square_tiny}}" /></span>
        </div>
        {{-- Game title and platform --}}
        <div>
          <span class="selected-game-title">
            <strong>{{$game->name}}</strong>@if($game->release_date)<span class="release-year m-l-5">{{$game->release_date->format('Y')}}</span>@endif
          </span>
          <span class="platform-label" style="background-color:{{$game->platform->color}}; color:{{$game->platform->text_color}}">
            {{$game->platform->name}}
          </span>
        </div>
      </div>
      {{-- End selected game panel --}}

      <div class="modal-body">
        <div class="main-content collect-content">
            <div id="collect-clone" class="collect-div">
              <div class="collect-span">
                <label class="collect-qty-label">Quantity</label>
                <input class="dark-input collect-input collect-qty" type="number" min="1" value="1">
                <input type="hidden" class="collect-id" value="">
                <span style="float:right;"><span class="collect-load" style="padding-right: 5px"></span><i class="fa fa-trash collect-delete" style="color: crimson; cursor: pointer"></i> </span>
              </div>
              @foreach(config('components.all') as $component)
                @if($game->{$component})
                  <div class="collect-component">
                    <label class="collect-label">
                    <input class="dark-input collect-checkbox component-{{$component}}" type="checkbox" data-value="{{$component}}">
                    {{ trans("games.components.$component") }}</label>
                  </div>
                @endif
              @endforeach
              <div>
                @foreach(regionCodes() as $region)
                  @if($region == 'ntsc_u' && !is_null($game->ntsc_u))
                    <button data-value="ntsc_u" class="btn region-btn region-btn-us collect-region collect-ntsc_u" style="opacity: 40%; margin-bottom: 5px">
                      <span class="flag-container">
                        <img src="{{ asset('img/flags/US.svg') }}" height="20"/>
                      </span>
                      <span class="region-btn-name">US</span>
                    </button>
                  @elseif($region == 'pal' && !is_null($game->pal))
                    <button data-value="pal" class="btn region-btn region-btn-eu collect-region collect-pal" style="opacity: 40%; margin-bottom: 5px">
                      <span class="flag-container">
                          <img src="{{ asset('img/flags/EU.svg') }}" height="20"/>
                      </span>
                      <span class="region-btn-name">EU</span>
                    </button>
{{--                  @elseif($region == 'ntsc_j' && !is_null($game->ntsc_j))--}}
{{--                    <button data-value="ntsc_j" class="btn region-btn region-btn-jp collect-region collect-ntsc_j" style="opacity: 40%; margin-bottom: 5px">--}}
{{--                      <span class="flag-container">--}}
{{--                          <img src="{{ asset('img/flags/JP.svg') }}" height="20"/>--}}
{{--                      </span>--}}
{{--                      <span class="region-btn-name">JP</span>--}}
{{--                    </button>--}}
                  @elseif($region == 'pa' && !is_null($game->pa))
                    <button data-value="pa" class="btn region-btn region-btn-pa collect-region collect-pa" style="opacity: 40%; margin-bottom: 5px">
                      <span class="flag-container">
                          <img src="{{ asset('img/flags/Play-Asia.jpg') }}" height="20"/>
                      </span>
                      <span class="region-btn-name region-btn-playasia">playasia</span>
                    </button>
                  @endif
                @endforeach
              </div>
            </div>
          <button id="collection-add" class="btn btn-primary">Add</button>
        </div>
      </div>
    </div>
  </div>
</div>
{{-- End Modal for for new messages --}}


@push('scripts')
<script src="{{ asset('js/autoNumeric.min.js') }}"></script>
<script type="text/javascript">
  $(document).on("click", ".collect-region", function () {
    $(this).closest(".collect-div").find(".collect-region").css('opacity', '40%');
    let data = {};
    data["id"] = $(this).closest(".collect-div").find(".collect-id").val();
    data["region"] = $(this).attr('data-value');
    let self = $(this)
    load_icon = $(this).closest(".collect-div").find('.collect-load')
    $.ajax({
      url: "/havelist/update",
      type: 'POST',
      data: data,
      headers: { 'X-CSRF-TOKEN': Laravel.csrfToken },
      success: function(result) {
        self.css('opacity', '100%')
      },
      beforeSend: function() {
        load_icon.html('<i class="fa fa-spinner fa-pulse fa-fw"></i>')
      },
      complete: function() {
        setTimeout(function () {
          load_icon.html('<i class="fa fa-save"></i>')
          setTimeout(function () {
            load_icon.html('')
          }, 3000)
        }, 1000)
      },
    });
  });

$(document).on("click", ".collect-checkbox", function () {
  let data = {};
  data["id"] = $(this).closest(".collect-div").find(".collect-id").val();
  data[$(this).attr('data-value')] = $(this).is(":checked") ? 1 : 0;
  load_icon = $(this).closest(".collect-div").find('.collect-load')
  $.ajax({
    url: "/havelist/update",
    type: 'POST',
    data: data,
    headers: { 'X-CSRF-TOKEN': Laravel.csrfToken },
    success: function(result) {
      console.log('result', result)
    },
    beforeSend: function() {
      load_icon.html('<i class="fa fa-spinner fa-pulse fa-fw"></i>')
    },
    complete: function() {
      setTimeout(function () {
        load_icon.html('<i class="fa fa-save"></i>')
        setTimeout(function () {
          load_icon.html('')
        }, 3000)
      }, 1000)
    },
  });
});
$(document).on("change", ".collect-qty", function () {
    let data = {};
    data["id"] = $(this).closest(".collect-div").find(".collect-id").val();
    data["quantity"] = $(this).val();
    console.log('data', data)
    load_icon = $(this).closest(".collect-div").find('.collect-load')
    $.ajax({
      url: "/havelist/update",
      type: 'POST',
      data: data,
      headers: { 'X-CSRF-TOKEN': Laravel.csrfToken },
      success: function(result) {
        console.log('result', result)
      },
      beforeSend: function() {
        load_icon.html('<i class="fa fa-spinner fa-pulse fa-fw"></i>')
      },
      complete: function() {
        setTimeout(function () {
          load_icon.html('<i class="fa fa-save"></i>')
          setTimeout(function () {
            load_icon.html('')
          }, 3000)
        }, 1000)
      },
    });
});

$(document).on("click", ".collect-delete", function () {
    let data = {};
    let collect_item = $(this).closest(".collect-div");
    let id = collect_item.find(".collect-id").val();
    $.ajax({
      url: "/havelist/delete/"+id,
      type: 'DELETE',
      headers: { 'X-CSRF-TOKEN': Laravel.csrfToken },
      success: function(result) {
        collect_item.slideUp()
        setTimeout(function () {
          collect_item.remove()
        }, 1000)
      },
    });
});

$(document).ready(function(){
  $("#collect-clone").hide();
  function buildCollection() {
    collection = JSON.parse('{!! json_encode($game->collection) !!}')
    components = JSON.parse('{!! json_encode(config('components.all')) !!}')
    for (index = 0; index < collection.length; ++index) {
      collection[index];

      let clone = $("#collect-clone").clone()
      let collect_id = clone.find('.collect-id').val(collection[index]["id"])
      let quantity = clone.find('.collect-qty').val(collection[index]["quantity"])

      clone.find('.collect-'+collection[index]["region"]).css('opacity', '100%')
      for (c = 0; c < components.length; ++c) {
        if (collection[index][components[c]] == 1) {
          clone.find('.component-'+components[c]).prop("checked", true);
        }
      }
      $("#collection-add").before(clone)
      clone.show()
    }
  }
  buildCollection()

  $("#collection-add").on("click", function () {
    $.ajax({
      url: "/havelist/create",
      type: 'POST',
      data: { product_id: {{$game->id}} },
      headers: { 'X-CSRF-TOKEN': Laravel.csrfToken },
      success: function(result) {
        let clone = $("#collect-clone").clone()
        let collect_id = clone.find('.collect-id').val(result.list.id)
        console.log('region', '.collect-'+result.list.region);
        clone.find('.collect-'+result.list.region).css('opacity', '100%')
        $("#collection-add").before(clone)
        clone.slideDown()
        console.log('result', result)
      }
    });
  })


  $(".clt-save-btn").on('click', function () {
    console.log('save')
    let collect_row = $(this).closest(".collect-div")
    console.log('parent', collect_row.find('.collect-id').val())

    $.ajax({
      url: "/games/havelist/update",
      type: 'POST',
      data: { foo: 'bar' },
      headers: { 'X-CSRF-TOKEN': Laravel.csrfToken },
      beforeSend: function() {
        // TODO: show your spinner
        //$('#loading').show();
      },
      complete: function() {
        // TODO: hide your spinner
        //$('#loading').hide();
      },
      success: function(result) {
        //$(targ).html(result);
        console.log('result', result)
      }
    });

  })


  {{-- Wishlist submit --}}
  $("#send-wishlist{{ isset($game->collection[0]) ? '-' . $game->collection[0]->id : '' }}").click( function(){
    $('#send-wishlist{{ isset($game->collection[0]) ? '-' . $game->collection[0]->id : '' }} span').html('<i class="fa fa-spinner fa-pulse fa-fw"></i>');
    $('#send-wishlist{{ isset($game->collection[0]) ? '-' . $game->collection[0]->id : '' }}').addClass('loading');
    $('#form-new-wishlist{{ isset($game->collection[0]) ? '-' . $game->collection[0]->id : '' }}').submit();
  });

  {{-- Start mask prices for money input --}}
  const autoNumericOptions = {
      digitGroupSeparator        : '{{ Currency(Config::get('settings.currency'))->getThousandsSeparator() }}',
      decimalCharacter           : '{{ Currency(Config::get('settings.currency'))->getDecimalMark() }}',
  };

  {{-- Initialization --}}
  $('.wishlist_price').autoNumeric('init', autoNumericOptions);

  {{-- Open maximal price input if notifications are enabled --}}
  $('#wishlist-notification{{ isset($game->collection[0]) ? '-' . $game->collection[0]->id : '' }}').click(function() {
    if( $(this).is(':checked')) {
      $("#max-price{{ isset($game->collection[0]) ? '-' . $game->collection[0]->id : '' }}").slideDown('fast');
    } else {
      $("#max-price{{ isset($game->collection[0]) ? '-' . $game->collection[0]->id : '' }}").slideUp('fast');
    }
  });

});
</script>
@endpush
