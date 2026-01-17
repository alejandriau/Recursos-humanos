
@foreach($secretarias as $secretaria)
<option value="{{ $secretaria->id }}">{{ $secretaria->denominacion }}</option>
@endforeach
