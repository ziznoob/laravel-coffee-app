<header>
    <nav class="navbar navbar-expand-md bg-white shadow-sm">
        <div class="container">
            <a href="{{ url('/') }}" class="navbar-brand fw-semibold">{{ config('app.name') }}</a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav"
                    aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav ms-auto align-items-md-center gap-md-2">
                    <li class="nav-item">
                        <a href="{{ route('products.index') }}"
                           @class(['nav-link', 'active fw-semibold' => request()->routeIs('products.*')])>Shop</a>
                    </li>

                    @inject('cart', 'App\Services\CartService')
                    <li class="nav-item">
                        <a href="{{ route('cart.index') }}"
                           @class(['nav-link d-flex align-items-center gap-1', 'active fw-semibold' => request()->routeIs('cart.*')])>
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-cart" viewBox="0 0 16 16" aria-hidden="true">
                                <path d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .491.592l-1.5 8A.5.5 0 0 1 13 12H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5M3.102 4l1.313 7h8.17l1.313-7zM5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4m7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4m-7 1a1 1 0 1 1 0 2 1 1 0 0 1 0-2m7 0a1 1 0 1 1 0 2 1 1 0 0 1 0-2"/>
                            </svg>
                            <span class="visually-hidden">Cart</span>
                            {{-- updated by Cart.jsx --}}
                            <span data-cart-count @if ($cart->count() === 0) hidden @endif
                                  class="badge rounded-pill text-bg-warning">{{ $cart->count() }}</span>
                        </a>
                    </li>

                    <li class="nav-item dropdown">
                        <a href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"
                           @class(['nav-link d-flex align-items-center gap-1', 'active' => request()->routeIs('login', 'admin.*')])>
                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" class="bi bi-person" viewBox="0 0 16 16" aria-hidden="true">
                                <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6m2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0m4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4m-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10s-3.516.68-4.168 1.332c-.678.678-.83 1.418-.832 1.664z"/>
                            </svg>
                            <span class="visually-hidden">Account</span>
                        </a>

                        <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                            @auth
                                <li><h6 class="dropdown-header">{{ auth()->user()->name }}</h6></li>
                                <li>
                                    <a href="{{ route('admin.dashboard') }}"
                                       @class(['dropdown-item', 'active' => request()->routeIs('admin.dashboard')])>Dashboard</a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.orders.index') }}"
                                       @class(['dropdown-item', 'active' => request()->routeIs('admin.orders.*')])>Orders</a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('admin.logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger">Log out</button>
                                    </form>
                                </li>
                            @else
                                <li>
                                    <a href="{{ route('login') }}"
                                       @class(['dropdown-item', 'active' => request()->routeIs('login')])>Log in</a>
                                </li>
                            @endauth
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</header>
