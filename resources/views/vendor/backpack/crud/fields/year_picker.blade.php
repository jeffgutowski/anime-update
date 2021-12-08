<!-- html5 week input -->
<div @include('crud::inc.field_wrapper_attributes') >
    <label>{!! $field['label'] !!}</label>
    @include('crud::inc.field_translatable_icon')
  <select name="{{ $field['name'] }}" @include('crud::inc.field_attributes')>
  @if (is_null($field['value']))
        <option value="" selected>----</option>
  @else
    <option value="">----</option>
  @endif
  @for($year = (int)date('Y'); (isset($field['year']) ? $field['year'] : 1900) <= $year; $year--)
    @if ($year == substr($field['value'], 0, 4))
        <option value="{{$field['value']}}" selected>{{$year}}</option>
    @else
        <option value="{{$year}}-01-01">{{$year}}</option>
    @endif
  @endfor
  </select>
    {{-- HINT --}}
    @if (isset($field['hint']))
        <p class="help-block">{!! $field['hint'] !!}</p>
    @endif
</div>