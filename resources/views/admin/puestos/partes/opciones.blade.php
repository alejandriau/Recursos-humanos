
@foreach($options as $option)
    <option value="{{ $option->id }}">{{ $option->denominacion }}</option>
@endforeach

