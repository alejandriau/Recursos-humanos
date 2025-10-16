@if(isset($options) && count($options) > 0)
    @foreach($options as $value => $label)
        <option value="{{ $value }}"
                {{ (isset($selected) && $selected == $value) ? 'selected' : '' }}
                {{ (isset($disabled) && $disabled == $value) ? 'disabled' : '' }}>
            {{ $label }}
        </option>
    @endforeach
@else
    <option value="">No hay opciones disponibles</option>
@endif
