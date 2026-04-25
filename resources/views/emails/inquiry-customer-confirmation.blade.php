<p>Hallo {{ $inquiry->first_name }} {{ $inquiry->last_name }},</p>

<p>vielen Dank fuer Ihre Anfrage. Wir haben folgende Positionen erhalten:</p>

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

<p>Wir melden uns zeitnah bei Ihnen.</p>
