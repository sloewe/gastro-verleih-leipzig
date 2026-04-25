<x-mail::message>
# Hallo {{ $inquiry->first_name }} {{ $inquiry->last_name }},

vielen Dank fuer Ihre Anfrage. Wir haben folgende Positionen erhalten:

@foreach ($inquiry->products as $product)
- {{ $product->name }} - Menge: {{ $product->pivot->quantity }}@if ($product->pivot->feature_value) ({{ $product->pivot->feature_value }})@endif
@endforeach


Wir melden uns zeitnah bei Ihnen.
</x-mail::message>
