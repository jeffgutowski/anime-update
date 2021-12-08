<!-- html5 date input -->

<?php
// if the column has been cast to Carbon or Date (using attribute casting)
// get the value as a date string
if (isset($field['value']) && ( $field['value'] instanceof \Carbon\Carbon || $field['value'] instanceof \Jenssegers\Date\Date )) {
    $field['value'] = $field['value']->toDateString();
}
$id = 'x'.uniqid();
?>

<div @include('crud::inc.field_wrapper_attributes') >
    <label>{!! $field['label'] !!}</label>
    @include('crud::inc.field_translatable_icon')
    <div class="input-group date">
        <input
            type="date"
            name="{{ $field['name'] }}"
            id="{{ $id }}"
            value="{{ old($field['name']) ? old($field['name']) : (isset($field['value']) ? $field['value'] : (isset($field['default']) ? $field['default'] : '' )) }}"
            @include('crud::inc.field_attributes')
        >
        <div class="input-group-addon remove">
            <span class="clear-calendar clear-{{ $id }} glyphicon glyphicon-remove"></span>
        </div>
    </div>

    {{-- HINT --}}
    @if (isset($field['hint']))
        <p class="help-block">{!! $field['hint'] !!}</p>
    @endif
</div>
<script type="text/javascript">
    var clear = document.querySelector('.clear-calendar.clear-<?= $id ?>');
    clear.addEventListener('click', function() {
        document.querySelector('#<?= $id ?>').value = '';
    });
</script>
<style>
    .clear-calendar {
        cursor: pointer;
    }
</style>
