@extends(Theme::getLayout())

@section('content')
  {{-- Open form for listing edit --}}
  @if(request()->segment(2) == 'review')
    {{ Form::open(array('url'=>'suggestion/approve', 'id'=>'approve', 'role'=>'form', 'files' => true )) }}
  @else
    {{ Form::open(array('url'=>'suggestion/submit', 'id'=>'suggestion', 'role'=>'form', 'files' => true )) }}
  @endif
  <div class="listing-form">
    <div class="panel">

      {{-- Panel Title (Details) --}}
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-pencil m-r-5"></i>Suggestion Form
        @if(isset($before))
          <span> — {{$before->true_name}}</span>
        @elseif (isset($after))
          <span> — {{$after->true_name}}</span>
        @else
            — New
        @endif
        </h3>
      </div>

      <div class="panel-body">
        <div class="row no-space">
          <input id="platforms" type="hidden" value="{{$platforms}}">
          @if(request()->segment(2) == 'review')
            <input id="product_id" name="product_id" type="hidden" value="{{isset($after) ? $after->product_id : null}}">
            <input id="suggestion_id" name="suggestion_id" type="hidden" value="{{isset($after) ? $after->id : null}}">
          @else
            {{-- When submitting a suggestion set the product_id to the product's id --}}
            <input id="product_id" name="product_id" type="hidden" value="{{isset($after) ? $after->id : null}}">
          @endif
          {{-- name --}}
          <div>
            <div class="input-header">Name</div>
            <div class="input-before">{{isset($before) ? $before->true_name : null}}</div>
            <input id="name" name="name" class="dark-input width-100 {{isset($before) && $before->true_name != $after->true_name ? 'input-diff' : ''}}" type="text" value="{{isset($after) ? $after->true_name : ''}}">
          </div>
          <br/>
          {{-- description --}}
          <div>
            <div class="input-header">Description</div>
            <div class="input-before">{{isset($before) ? $before->description : null}}</div>
            <textarea id="description" name="description" class="dark-input input-textarea {{isset($before) && $before->description != $after->description ? 'input-diff' : ''}}" placeholder="">{{isset($after) ? $after->description : null }}</textarea>
          </div>
          <br/>
          {{-- platform_id --}}
          <div>
            <div class="input-header">Platform</div>
            <div class="input-before">{{isset($before) ? $before->platform_name : null}}</div>
            <select class="dark-input {{isset($before) && $before->platform_id != $after->platform_id ? 'input-diff' : ''}}" name="platform_id" id="platform_id">
              <option value="" selected disabled></option>
              @foreach($platforms as $platform)
                <option value="{{$platform->id}}" {{isset($after) && $after->platform_id == $platform->id ? 'selected' : ''}}>{{$platform->name}}</option>
              @endforeach
            </select>
          </div>
          <br/>
          {{-- type --}}
          <div>
            <div class="input-header">Product Type</div>
            <div class="input-before">{{isset($before) ? ucfirst($before->type) : null}}</div>
            <select class="dark-input {{isset($before) && $before->type != $after->type ? 'input-diff' : ''}}" name="type" id="type">
              <option value="game" {{isset($after) && $after->type == 'game'}}>Game</option>
              @foreach($types as $type)
                <option value="{{$type->slug}}" {{isset($after) && $after->type == $type->name ? 'selected' : ''}}>{{$type->name}}</option>
              @endforeach
            </select>
          </div>
          <br/>
          {{-- company --}}
          <div class="hardware-input hidden">
            <div class="input-header">Manufacturer</div>
            <div class="input-before">{{isset($before) ? $before->company : null}}</div>
            <input id="company" name="company" class="dark-input {{isset($before) && $before->company != $after->company ? 'input-diff' : ''}}" type="text" value="{{isset($after) ? $after->company : ''}}">
            <br/>
            <br/>
          </div>
          {{-- model_number --}}
          <div class="hardware-input hidden">
            <div class="input-header">Model Number</div>
            <div class="input-before">{{isset($before) ? $before->model_number : null}}</div>
            <input id="model_number" name="model_number" class="dark-input {{isset($before) && $before->model_number != $after->model_number ? 'input-diff' : ''}}" type="text" value="{{isset($after) ? $after->model_number : ''}}">
            <br/>
            <br/>
          </div>
          <div class="game-input">
            <div class="input-header">Components</div>
            <div class="input-before">{{isset($before) ? implode(', ', $before->components) : null}}</div>
            <div class="filter-box {{isset($before) && $before->components != $after->components ? 'input-diff' : ''}}">
              @foreach(config('components.all') as $component)
                <span id="{{$component}}-box" class="component-wrapper {{isset($after) && !is_null($after->$component) ? 'component-show' : 'hidden'}}">
                 <input class="component-input" id="{{$component}}" name="{{$component}}" type="checkbox" {{isset($after) && $after->$component == true ? 'checked' : ''}}>
                 <label class="checkbox-label" for="{{$component}}">{{trans("games.components.$component")}}</label>
                </span>
              @endforeach
            </div>
          </div>
          <div class="game-input">
            <div class="input-header">Developers</div>
            <input type="hidden" name="developers" value="{{isset($after->developers) ? json_encode($after->developers) : '{}'}}"></input>
            @if(isset($before) && isset($before->developers))
              <div>
                {{implode(', ', json_decode(json_encode($before->developers), JSON_OBJECT_AS_ARRAY))}}
              </div>
            @endif
            <input class="dark-input" type='text' id='dev-search' list='developers' placeholder="Search for Developers" >
            <datalist id="developers"></datalist>
            <span class="selected-list" id="selected-developers">
              @if(isset($after))
                @foreach($after->developers as $id => $name)
                  <span onclick="removeDeveloper(this, 'developer', {{$id}})" data-value="{{$name}}" class="{{isset($before->developers) && isset($after->developers) && json_encode($before->developers) != json_encode($after->developers) ? 'input-diff' : ''}} clickable label platform-label platform-filter m-r-5 m-b-5 inline-block platform-filter-active"><i class="fas fa-times-circle search-select"></i>{{$name}}</span>
                @endforeach
              @endif
            </span>
            <br/>
            <br/>
          </div>
        {{-- genres --}}
        <div class="game-input">
          <div class="input-header">Genres</div>
          <div class="input-before">{{isset($before) ? implode(", ", $before->genres) : null}}</div>
            <div class="filter-box {{isset($before) && (!empty(array_diff($after->genres, $before->genres)) || ! empty(array_diff($before->genres, $after->genres))) ? 'input-diff' : ''}}">
              @foreach($genres as $genre)
                <span class="filter-item">
                 <input class="" id="genre-{{$genre->id}}" name="genre-{{$genre->id}}" type="checkbox" {{isset($after) && in_array($genre->name, $after->genres) ? 'checked' : ''}}>
                 <label class="checkbox-label" for="genre-{{$genre->id}}">{{$genre->name}}</label>
              </span>
              @endforeach
            </div>
          </div>
          <br/>
          <div>
            <div>
              <span class="region-tabs region-selected" content="us-tab">
                US (NTSC-U)
              </span>
              <span class="region-tabs" content="eu-tab">
                EU (PAL)
              </span>
              <span class="region-tabs" content="jp-tab">
                JP (NTSC-J)
              </span>
            </div>
            <div id="us-tab" class="region-content">
                {{-- cover_us --}}
                <div class="input-header">US Cover Image</div>
                <div>
                  <input id="cover_us" name="cover_us" type='file' onchange="readImage(this, 'us')"/>
                </div>
                <br/>
              <input name="cover_us_url" value="{{isset($after) ? $after->cover_us : null}}" type="hidden">
              @if(isset($before) && $before->cover_us != $after->cover_us)
                <div>
                  <img class="upload-image" src="{{$before->cover_us}}" />
                </div>
                <br/>
                @endif
                <div>
                  <img class="upload-image {{isset($after) ? '' : 'hidden'}} {{isset($before) && isset($after) && !is_null($after->cover_us) && $before->cover_us != $after->cover_us ? 'input-diff' : ''}}" id="us-image" src="{{isset($after) ? $after->cover_us : ''}}" />
                  <div class="image-placeholder {{isset($after) ? 'hidden' : ''}}"><span class="image-placeholder-text">IMAGE PLACEHOLDER</span></div>
                </div>
                <br/>
                {{-- name_us --}}
                <div>
                  <div class="input-header">US Region Name</div>
                  <div class="input-before">{{isset($before) ? $before->name_us : null}}</div>
                  <input id="name_us" name="name_us" class="dark-input width-100 {{isset($before) && $before->name_us != $after->name_us ? 'input-diff' : ''}}" id="us-region-name" type="text" value="{{isset($after) ? $after->name_us : ''}}">
                </div>
                <br/>
                {{-- ntsc_u --}}
                <div>
                  <div class="input-header">US Release Date</div>
                  <div class="input-before">{{isset($before->ntsc_u) ? date("m/d/Y", strtotime($before->ntsc_u)) : ''}}</div>
                  <input id="ntsc_u" name="ntsc_u" class="dark-input {{isset($before) && $before->ntsc_u != $after->ntsc_u ? 'input-diff' : ''}}" id="us-release-date" type="date" value="{{isset($after) ? $after->ntsc_u : ''}}">
                </div>
                <br/>
                {{-- upc_us --}}
                <div>
                  <div class="input-header">US UPC</div>
                  <div class="input-before">{{isset($before) ? $before->upc_us : null}}</div>
                  <input id="upc_us" name="upc_us" class="dark-input {{isset($before) && $before->upc_us != $after->upc_us ? 'input-diff' : ''}}" id="us-upc" type="text" value="{{isset($after) ? $after->upc_us : ''}}">
                </div>
                <br/>
                {{-- catalog_number_us --}}
                <div class="game-input">
                  <div class="input-header">US Catalog Number</div>
                  <div class="input-before">{{isset($before) ? $before->catalog_number_us : null}}</div>
                  <input id="catalog_number_us" name="catalog_number_us" class="dark-input {{isset($before) && $before->catalog_number_us != $after->catalog_number_us ? 'input-diff' : ''}}" id="us-catalog-number" type="text" value="{{isset($after) ? $after->catalog_number_us : ''}}">
                </div>
                <br/>
                <div class="game-input">
                  <div class="input-header">US Publishers</div>
                  <input type="hidden" name="publishers_us" value="{{isset($after) ? json_encode($after->publishers_us) : '{}'}}">
                  @if(isset($before) && isset($before->publishers_us))
                    <div>
                      {{implode(', ', json_decode(json_encode($before->publishers_us), JSON_OBJECT_AS_ARRAY))}}
                    </div>
                  @endif
                  <input class="dark-input publisher-search" type='text' id='publisher-search-form' list='publishers_us' placeholder="Search for Publishers">
                  <datalist id="publishers_us" class="publisher-list"></datalist>
                  <span class="selected-list {{isset($before->publishers_us) && isset($after->publishers_us) && json_encode($before->publishers_us) != json_encode($after->publishers_us) ? 'input-diff' : ''}}" id="selected-publishers_us">
                  @if(isset($after))
                      @foreach($after->publishers_us as $id => $name)
                        <span onclick="removeFilter(this, 'publishers_us', {{$id}})" data-value="{{$id}}" class="clickable label platform-label platform-filter m-r-5 m-b-5 inline-block platform-filter-active"><i class="fas fa-times-circle search-select"></i>{{$name}}</span>
                      @endforeach
                    @endif
                  </span>
                </div>
            </div>
            <div id="eu-tab" class="region-content hidden">
              {{-- cover_eu --}}
              <div class="input-header">EU Cover Image</div>
              <div>
                <input id="cover_eu" name="cover_eu" type='file' onchange="readImage(this, 'eu');" />
              </div>
              <br/>
              <input name="cover_eu_url" value="{{isset($after) ? $after->cover_eu : null}}" type="hidden">
            @if(isset($before)&& $before->cover_eu != $after->cover_eu)
                <div>
                  <img class="upload-image" id="us-image" src="{{$before->cover_eu}}" />
                </div>
                <br/>
              @endif
              <div>
                <img class="upload-image {{isset($after) ? '' : 'hidden'}} {{isset($before) && isset($after) && !is_null($after->cover_eu) && $before->cover_eu != $after->cover_eu ? 'input-diff' : ''}}" id="eu-image" src="{{isset($after) ? $after->cover_eu : ''}}" />
                <div class="image-placeholder {{isset($after) ? 'hidden' : ''}}"><span class="image-placeholder-text">IMAGE PLACEHOLDER</span></div>
              </div>
              <br/>
              {{-- name_eu --}}
              <div>
                <div class="input-header">EU Region Name</div>
                <div class="input-before">{{isset($before) ? $before->name_eu : null}}</div>
                <input id="name_eu" name="name_eu" class="dark-input width-100 {{isset($before) && $before->name_eu != $after->name_eu ? 'input-diff' : ''}}" id="eu-region-name" type="text" value="{{isset($after) ? $after->name_eu : ''}}">
              </div>
              <br/>
              {{-- pal --}}
              <div>
                <div class="input-header">EU Release Date</div>
                <div class="input-before">{{isset($before->pal) ? date("m/d/Y", strtotime($before->pal)) : ''}}</div>
                <input id="pal" name="pal" class="dark-input {{isset($before) && $before->pal != $after->pal ? 'input-diff' : ''}}" id="eu-release-date" type="date" value="{{isset($after) ? $after->pal : ''}}">
              </div>
              <br/>
              {{-- upc_eu --}}
              <div>
                <div class="input-header">EU UPC</div>
                <div class="input-before">{{isset($before) ? $before->upc_eu : null}}</div>
                <input id="upc_eu" name="upc_eu" class="dark-input {{isset($before) && $before->upc_eu != $after->upc_eu ? 'input-diff' : ''}}" id="eu-upc" type="text" value="{{isset($after) ? $after->upc_eu : ''}}">
              </div>
              <br/>
              {{-- catalog_number_eu --}}
              <div class="game-input">
                <div class="input-header">EU Catalog Number</div>
                <div class="input-before">{{isset($before) ? $before->catalog_number_eu : null}}</div>
                <input id="catalog_number_eu" name="catalog_number_eu" class="dark-input {{isset($before) && $before->catalog_number_eu != $after->catalog_number_eu ? 'input-diff' : ''}}" id="eu-catalog-number" type="text" value="{{isset($after) ? $after->catalog_number_eu : ''}}">
              </div>
              <br/>

              {{-- publishers_eu --}}
              <div class="game-input">
                <div class="input-header">EU Publishers</div>
                <input type="hidden" name="publishers_eu" value="{{isset($after) ? json_encode($after->publishers_eu) : '{}'}}">
                @if(isset($before) && isset($before->publishers_eu))
                  <div>
                    {{implode(', ', json_decode(json_encode($before->publishers_eu), JSON_OBJECT_AS_ARRAY))}}
                  </div>
                @endif
                <input class="dark-input publisher-search" name="publisher-search-form" type='text' id='publisher-search-form' list='publishers_eu' placeholder="Search for Publishers">
                <datalist id="publishers_eu" class="publisher-list"></datalist>
                <span class="selected-list {{isset($before->publishers_eu) && isset($afte->dever->publishers_eu) && json_encode($before->publishers_eu) != json_encode($after->publishers_eu) ? 'input-diff' : ''}}" id="selected-publishers_eu">
                  @if(isset($after))
                    @foreach($after->publishers_eu as $id => $name)
                      <span onclick="removeFilter(this, 'publishers_eu', {{$id}})" data-value="{{$id}}" class="clickable label platform-label platform-filter m-r-5 m-b-5 inline-block platform-filter-active"><i class="fas fa-times-circle search-select"></i>{{$name}}</span>
                    @endforeach
                  @endif
                </span>
              </div>


            </div>
            <div id="jp-tab" class="region-content hidden">
              {{-- cover_jp --}}
              <div class="input-header">JP Cover Image</div>
              <div>
                <input id="cover_jp" name="conver_jp" type='file' onchange="readImage(this, 'jp');" />
              </div>
              <br/>
              <input name="cover_jp_url" value="{{isset($after) ? $after->cover_jp : null}}" type="hidden">
              @if(isset($before) && $before->cover_jp != $after->cover_jp)
                <div>
                  <img class="upload-image" id="us-image" src="{{$before->cover_jp}}" />
                </div>
                <br/>
              @endif
              <div>
                <img class="upload-image {{isset($after) ? '' : 'hidden'}} {{isset($before) && isset($after) && !is_null($after->cover_jp)  && $before->cover_jp != $after->cover_jp ? 'input-diff' : ''}}" id="jp-image" src="{{isset($after) ? $after->cover_jp : ''}}" />
                <div class="image-placeholder {{isset($after) ? 'hidden' : ''}}"><span class="image-placeholder-text">IMAGE PLACEHOLDER</span></div>
              </div>
              <br/>
              {{-- name_jp --}}
              <div>
                <div class="input-header">JP Region Name</div>
                <div class="input-before">{{isset($before) ? $before->name_jp : null}}</div>
                <input id="name_jp" name="name_jp" class="dark-input width-100 {{isset($before) && $before->name_jp != $after->name_jp ? 'input-diff' : ''}}" id="jp-region-name" type="text" value="{{isset($after) ? $after->name_jp : ''}}">
              </div>
              <br/>
              {{-- ntsc_j --}}
              <div>
                <div class="input-header">JP Release Date</div>
                <div class="input-before">{{isset($before->ntsc_j) ? date("m/d/Y", strtotime($before->ntsc_j)) : ''}}</div>
                <input id="ntsc_j" name="ntsc_j" class="dark-input {{isset($before) && $before->ntsc_j != $after->ntsc_j ? 'input-diff' : ''}}" id="jp-release-date" type="date" value="{{isset($after) ? $after->ntsc_j : ''}}">
              </div>
              <br/>
              {{-- upc_jp --}}
              <div>
                <div class="input-header">JP UPC</div>
                <div class="input-before">{{isset($before) ? $before->upc_jp : null}}</div>
                <input id="upc_jp" name="upc_jp" class="dark-input {{isset($before) && $before->upc_jp != $after->upc_jp ? 'input-diff' : ''}}" id="jp-upc" type="text" value="{{isset($after) ? $after->upc_jp : ''}}">
              </div>
              <br/>
              {{-- catalog_number_jp --}}
              <div class="game-input">
                <div class="input-header">JP Catalog Number</div>
                <div class="input-before">{{isset($before) ? $before->catalog_number_jp : null}}</div>
                <input id="catalog_number_jp" name="catalog_number_jp" class="dark-input {{isset($before) && $before->catalog_number_jp != $after->catalog_number_jp ? 'input-diff' : ''}}" id="jp-catalog-number" type="text" value="{{isset($after) ? $after->catalog_number_jp : ''}}">
              </div>
              <br/>
              {{-- publishers_jp --}}
              <div class="game-input">
                <div class="input-header">JP Publishers</div>
                <input type="hidden" name="publishers_jp" value="{{isset($after) ? json_encode($after->publishers_jp) : '{}'}}">
                @if(isset($before) && isset($before->publishers_jp))
                  <div>
                    {{implode(', ', json_decode(json_encode($before->publishers_jp), JSON_OBJECT_AS_ARRAY))}}
                  </div>
                @endif
                <input class="dark-input publisher-search" name="publisher-search-form" type='text' id='publisher-search-form' list='publishers_jp' placeholder="Search for Publishers">
                <datalist id="publishers_jp" class="publisher-list"></datalist>
                <span class="selected-list {{isset($before->publishers_jp) && isset($after->publishers_jp) && json_encode($before->publishers_jp) != json_encode($after->publishers_jp) ? 'input-diff' : ''}}" id="selected-publishers_jp">
                  @if(isset($after))
                    @foreach($after->publishers_jp as $id => $name)
                      <span onclick="removeFilter(this, 'publishers_jp', {{$id}})" data-value="{{$id}}" class="clickable label platform-label platform-filter m-r-5 m-b-5 inline-block platform-filter-active"><i class="fas fa-times-circle search-select"></i>{{$name}}</span>
                    @endforeach
                  @endif
                </span>
              </div>
            </div>
          <div>
            <br/>
            <div>
              <div class="input-header">Comments</div>
              <textarea id="comments" name="comments" class="dark-input input-textarea" placeholder="Type any other comments or suggestions here">{{isset($after->comments) ? $after->comments : ''}}</textarea>
            </div>
            <br/>
            @if(request()->segment('2') == 'review')
              <div>
                <div class="input-header">Review Comments</div>
                <textarea id="review_comments" name="review_comments" class="dark-input input-textarea" placeholder="Make any comments here why the suggestion will be approved or disapproved"></textarea>
              </div>
              <br/>
              {{-- Dissaprove Button --}}
              <div class="btn btn-lg btn-danger" id="disapprove-button">Disapprove</div>
              {{-- Approve Button --}}
              <button style="float: right" class="btn btn-lg btn-success" type="submit" id="submit-button">Approve & Save</button></div>
            @else
              {{-- Save Button --}}
              <button style="float: right" class="btn btn-lg btn-success" type="submit" id="submit-button">Submit</button></div>
            @endif
        </div>
      </div>
    </div>
  </div>
  {{ Form::close() }}
  {{-- End Listing Form --}}
@stop

@section('after-scripts')
  <script type="text/javascript">
    function readImage(input, region) {
      if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
          $('#'+region+'-image').attr('src', e.target.result)
                  .css('max-height', '300px')
                  .show()
          $('#'+region+'-image').parent().find('.image-placeholder').hide()
        };
        reader.readAsDataURL(input.files[0]);
      }
    }
    function removePublisher(span, type, id) {
      span.remove()
      if (id !== undefined) {
        delete window.publishers[type][id]
      }
      $("input[name="+type+"]").val(JSON.stringify(window.publishers[type]))
    }
    function removeDeveloper(span, type, id) {
      span.remove()
      if (id !== undefined) {
        delete window.developers[id]
      }
      $("input[name=developers]").val(JSON.stringify(window.developers))
    }
    $(document).ready(function(){
      window.publishers = {
        'publishers_us': JSON.parse($("input[name=publishers_us]").val()),
        'publishers_eu': JSON.parse($("input[name=publishers_eu]").val()),
        'publishers_jp': JSON.parse($("input[name=publishers_jp]").val()),
      };
      window.developers = JSON.parse($("input[name=developers]").val());
      var components = {
        0:'disc',
        1:'cartridge',
        2:'box',
        3:'case',
        4:'manual',
        5:'case_art',
        6:'cartridge_holder',
        7:'clamshell',
        8:'box_or_case',
        9:'art_or_holder',
        10:'case_sticker',
        11:'insert',
        12:'styrofoam',
      };

      $('.region-tabs').on('click', function () {
        $('.region-tabs').removeClass('region-selected')
        $(this).addClass('region-selected')
        content_id = $(this).attr('content')
        $('.region-content').hide()
        $('#'+content_id).show()
      })

      $(".input-textarea").keyup(function(e) {
        $(this).height(30);
        $(this).height(this.scrollHeight + parseFloat($(this).css("borderTopWidth")) + parseFloat($(this).css("borderBottomWidth")));
      });
      $(".input-textarea").keyup();

      $("#type").change(function(){
        if ($(this).val() == 'game') {
          $(".hardware-input").slideUp(500, function() {$(".game-input").slideDown(1000)})
        } else {
          $(".game-input").slideUp(1000, function() {$(".hardware-input").slideDown(500)})
        }
      });

      $("#platform_id").change(function(){
        let platforms = JSON.parse($("#platforms").val());
        for (i = 0; i < platforms.length; i++) {
          if (platforms[i].id == $(this).val()) {
            platform = platforms[i]
            // reset component group to be hidden
            $(".component-wrapper").hide()
            for(x = 0; x <= 12; x++) {
              let component = components[x]
              let comp = $("#" + components[x] + "-box")
              if (platform[component] == true) {
                comp.addClass('component-show')
                $("#"+component).attr('checked', 'checked')
                comp.show()
              }
            }
          }
        }
      });


      $(".publisher-search").on("keyup", debounce(function(){
        var self = $(this)
        // get region list
        var list = self.attr('list')
        var typedValue = $(this).val()
        $("#"+list+" option").each(function(i, item) {
          if (typedValue === $(this).val()) {
            // clear input
            self.val("")
            // get key
            let selectedKey = $(this).attr('data-value')
            // get value
            let selectedValue = $(this).val()
            // append visual for publisher with removal on click
            $("#selected-"+list).append($('<span onclick="removePublisher(this, \''+list+'\', '+selectedKey+')" data-value="'+selectedKey+'" class="clickable label platform-label platform-filter m-r-5 m-l-5 m-b-5 inline-block platform-filter-active"><i class="fas fa-times-circle search-select"></i> '+selectedValue+'</span>'))
            // set publisher to object
            window.publishers[list][selectedKey] = selectedValue;
            // set region publishers to hidden input
            $("input[name="+list+"]").val(JSON.stringify(window.publishers[list]))
            return;
          }
        })
        $("#"+list).empty();
        $.ajax({
          url: "/api/publishers/active",
          data: {q: typedValue},
          success: function (response) {
            $.each(response.data, function(i, item){
              // appen options to list
              $("#"+list).append($("<option>").attr('data-value', item.id).text(item.name));
            })
          }
        });
      }, 500));

      $("#dev-search").on("keyup", debounce(function(){
        var typedValue = $(this).val()
        $("#developers option").each(function(i, item) {
          if (typedValue === $(this).val()) {
            $("#dev-search").val('');
            let selectedKey = $(this).attr('data-value')
            let selectedValue = $(this).val()
            $("#selected-developers").append($('<span onclick="removeDeveloper(this, \'developers\', '+selectedKey+')" data-value="'+selectedKey+'" class="clickable label platform-label platform-filter m-r-5 m-b-5 inline-block platform-filter-active"><i class="fas fa-times-circle search-select"></i> '+selectedValue+'</span>'))
            window.developers[selectedKey] = selectedValue
            $("input[name=developers]").val(JSON.stringify(window.developers))
            return;
          }
        })
        $("#developers").empty();
        $.ajax({
          url: "/api/developers/search",
          data: {q: typedValue},
          success: function (response) {
            $.each(response.data, function(i, item){
              $("#developers").append($("<option>").attr('data-value', item.id).text(item.name));
            })
          }
        });
      }, 500));

      $('#disapprove-button').on("click", function(){
        window.location = "/suggestion/disapprove/{{isset($after) ? $after->id : null}}?review_comments=" + $("#review_comments").val()
      })

    })
  </script>


@stop
