<p>Neue Anfrage von {{ $inquiry->first_name }} {{ $inquiry->last_name }}.</p>

<p>
    E-Mail: {{ $inquiry->email }}<br>
    Telefon: {{ $inquiry->phone ?: '-' }}<br>
    Firma: {{ $inquiry->company ?: '-' }}<br>
    Adresse: {{ $inquiry->street }}, {{ $inquiry->postal_code }} {{ $inquiry->city }}
</p>

@if ($inquiry->message)
    <p>Nachricht: {{ $inquiry->message }}</p>
@endif

<ul>
    @foreach ($inquiry->products as $product)
        <li>
            {{ $product->name }} - Menge: {{ $product->pivot->quantity }}
            @if ($product->pivot->feature_value)
                ({{ $product->pivot->feature_value }})
            @endif
        </li>
    @endforeach
</ul>
