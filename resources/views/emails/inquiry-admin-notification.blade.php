<x-mail::message>
# Neue Anfrage von {{ $inquiry->first_name }} {{ $inquiry->last_name }}

**E-Mail:** {{ $inquiry->email }}  
**Telefon:** {{ $inquiry->phone ?: '-' }}  
**Firma:** {{ $inquiry->company ?: '-' }}  
**Adresse:** {{ $inquiry->street }}, {{ $inquiry->postal_code }} {{ $inquiry->city }}

@if ($inquiry->message)
**Nachricht:** {{ $inquiry->message }}

@endif
## Angefragte Produkte

@foreach ($inquiry->products as $product)
- {{ $product->name }} - Menge: {{ $product->pivot->quantity }}@if ($product->pivot->feature_value) ({{ $product->pivot->feature_value }})@endif
@endforeach
</x-mail::message>
