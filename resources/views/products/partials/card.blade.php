<article class="card h-100 shadow-sm">
    @if ($product->image)
        <img src="{{ asset($product->image) }}" class="card-img-top object-fit-cover" style="height: 180px" alt="{{ $product->name }}">
    @endif

    <div class="card-body d-flex flex-column">
        <div class="d-flex align-items-center justify-content-between small">
            <span class="badge rounded-pill badge-bean">{{ $product->type->label() }}</span>
            @if ($product->roast_level)
                <span class="text-body-secondary">{{ ucfirst($product->roast_level) }} roast</span>
            @endif
        </div>

        <h2 class="h5 card-title mt-3 mb-0">{{ $product->name }}</h2>
        <p class="small text-body-secondary">{{ $product->origin }}</p>

        @if ($product->description)
            <p class="small">{{ $product->description }}</p>
        @endif

        @if ($product->flavour)
            <p class="small mb-1">
                <span class="fw-semibold">Notes:</span> {{ implode(', ', $product->flavour) }}
            </p>
        @endif

        @if ($product->brewMethods->isNotEmpty())
            <p class="small">
                <span class="fw-semibold">Best for:</span> {{ $product->brewMethods->pluck('name')->implode(', ') }}
            </p>
        @endif

        <ul class="list-unstyled d-flex gap-2 small mt-auto mb-0">
            @foreach ($product->variants as $variant)
                <li class="flex-fill border rounded px-2 py-1 text-center">
                    <span class="d-block text-body-secondary">{{ $variant->weightLabel() }}</span>
                    <span class="fw-semibold">{{ $variant->formattedPrice() }}</span>
                </li>
            @endforeach
        </ul>

        <form method="POST" action="{{ route('cart.items.store') }}" class="input-group mt-3">
            @csrf
            <label for="variant-{{ $product->id }}" class="visually-hidden">Size for {{ $product->name }}</label>
            <select id="variant-{{ $product->id }}" name="variant_id" class="form-select form-select-sm">
                @foreach ($product->variants as $variant)
                    <option value="{{ $variant->id }}" @disabled($variant->stock === 0)>
                        {{ $variant->weightLabel() }} — {{ $variant->formattedPrice() }}{{ $variant->stock === 0 ? ' (sold out)' : '' }}
                    </option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-dark btn-sm">Add to cart</button>
        </form>
    </div>
</article>
