@extends('layouts.app')

@section('title', 'Shop')

@section('content')
    <div class="container py-4">
        <div class="d-flex align-items-baseline justify-content-between mb-4">
            <h1 class="h3 mb-0">Our Coffee Beans</h1>
            <p class="small text-body-secondary mb-0">{{ $products->count() }} products</p>
        </div>

        @use('App\Enums\BeanType')

        <div class="card card-body shadow-sm mb-4">
            <div class="row g-3 align-items-center">
                <div class="col-lg-5">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-body-secondary">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16" aria-hidden="true">
                                <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"/>
                            </svg>
                        </span>
                        <input type="search" id="searchbar" class="form-control border-start-0 ps-0"
                            placeholder="Search by name, origin or flavour…" aria-label="Search coffee beans" autocomplete="off">
                    </div>
                </div>

                <div class="col-lg-7">
                    <div id="filter" class="d-flex flex-wrap gap-2 justify-content-lg-end" role="group" aria-label="Filter by bean type">
                        <button type="button" class="btn btn-sm btn-outline-dark rounded-pill px-3 active" data-type="">All</button>
                        @foreach (BeanType::cases() as $type)
                            <button type="button" class="btn btn-sm btn-outline-dark rounded-pill px-3" data-type="{{ $type->value }}">{{ $type->label() }}</button>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        @if ($products->isEmpty())
            <p class="text-body-secondary">No products available yet.</p>
        @else
            <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 g-4">
                @foreach ($products as $product)
                    <div class="col"
                        data-type="{{ $product->type->value }}"
                        data-search="{{ strtolower($product->name.' '.$product->origin.' '.implode(' ', $product->flavour ?? [])) }}">
                        @include('products.partials.card', ['product' => $product])
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection

@push('scripts')
<script>
    const searchbar = document.getElementById('searchbar');
    const filters = document.getElementById('filter').querySelectorAll('button');
    const cards = document.querySelectorAll('[data-search]');

    let selectedType = ''; // '' = All

    function applyFilters() {
        const term = searchbar.value.toLowerCase();

        cards.forEach((card) => {
            const matchesSearch = card.dataset.search.includes(term);
            const matchesType = selectedType === '' || card.dataset.type === selectedType;
            card.hidden = !(matchesSearch && matchesType);
        });
    }

    searchbar.addEventListener('input', applyFilters);

    filters.forEach((button) => {
        button.addEventListener('click', () => {
            selectedType = button.dataset.type;

            filters.forEach((b) => b.classList.remove('active'));
            button.classList.add('active');

            applyFilters();
        });
    });

</script>
@endpush

