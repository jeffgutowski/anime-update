@forelse($json_results as $result)
<section class="panel">
  <div class="panel-body">
      <div class="flex-center">
      {{-- Game Cover --}}
        <div class="m-r-20">
          <span class="">
              @if(isset($result['cover']))
              <img src="https://images.igdb.com/igdb/image/upload/t_cover_small/{{$result['cover']['image_id']}}.jpg">
              @else
              <img src="{{ asset('images/square_tiny/no_cover.jpg') }}" alt="No cover">
              @endif
          </span>
        </div>
        {{-- Game title & platform --}}
        <div>
          <div class="game-title">{{ $result['name'] }}</div>
          <div class="game-labels">
            <span class="platform-label m-r-5" style="background-color:black;">{{ $result['platform'] }}</span>
          </div>
        </div>
      </div>
    </div>

    <div class="panel-footer">
      {{-- Database status --}}
      <div class="in-database">
        {{ trans('games.add.results.in_database') }} <i class="fa {{ $result['in_database'] ? 'fa-check-circle text-success' : 'fa-times-circle text-danger' }}" aria-hidden="true"></i>
      </div>


        {{-- Details link for normal search --}}
        @if($result['in_database'])
        <a href="" class="button">
          <i class="fa fa-arrow-right" aria-hidden="true"></i> {{ trans('games.add.results.details') }}
        </a>
        {{-- Add game link for normal search --}}
        @else
        <form id="gameAdd-{{$loop->iteration}}" method="POST" novalidate="novalidate">
          <input type="hidden" name="platform" value="{{ $result['platform'] }}">
          <input type="hidden" name="name" value="{{ $result['name'] }}">
          <input type="hidden" name="game" value="{{ json_encode($result) }}">
          <a href="javascript:void(0)" class="button add-game" data-id="{{$loop->iteration}}">
            <i class="fa fa-plus" aria-hidden="true"></i> {{ trans('games.add.add_game') }}
          </a>
        </form>
        @endif
    </div>


</section>
@empty
@endforelse

<script type="text/javascript">
$(document).ready(function(){
  {{-- Open loading modal on game add --}}
  $(".add-game").click(function(event){
    var id = $(this).data('id');

    $('#modal_game_add').modal('show');
    setTimeout(function(){
      $.ajax({
          url:'{{ url("games/add") }}',
          type: 'POST',
          data:$('#gameAdd-' + id).serialize(),
          {{-- Send CSRF Token over ajax --}}
          headers: { 'X-CSRF-TOKEN': Laravel.csrfToken },
          success: function (data) {
            window.location=data;
          }
      });
     },500)

    // override browser following link when clicked
    return false;
  });
})
</script>