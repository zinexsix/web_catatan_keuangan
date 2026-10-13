{{-- Grouped icon picker used by the create/edit category modals. Expects $prefix ("create" or "edit"). --}}
@php
    // FontAwesome 6.4 (free, solid). Older names such as fa-tint are kept because default categories use them.
    $iconGroups = [
        'Makanan & Minuman' => [
            'fa-utensils', 'fa-mug-hot', 'fa-mug-saucer', 'fa-glass-water', 'fa-bottle-water', 'fa-blender',
            'fa-wine-glass', 'fa-martini-glass', 'fa-beer-mug-empty', 'fa-whiskey-glass', 'fa-burger',
            'fa-pizza-slice', 'fa-bowl-food', 'fa-bowl-rice', 'fa-drumstick-bite', 'fa-fish', 'fa-egg',
            'fa-ice-cream', 'fa-cookie', 'fa-cake-candles', 'fa-cheese', 'fa-apple-whole', 'fa-carrot', 'fa-lemon',
        ],
        'Belanja' => [
            'fa-shopping-bag', 'fa-cart-shopping', 'fa-basket-shopping', 'fa-store', 'fa-shirt', 'fa-socks',
            'fa-shoe-prints', 'fa-gem', 'fa-scissors', 'fa-tag', 'fa-receipt', 'fa-box',
        ],
        'Transportasi' => [
            'fa-car', 'fa-motorcycle', 'fa-bus', 'fa-taxi', 'fa-train', 'fa-plane', 'fa-ship', 'fa-bicycle',
            'fa-gas-pump', 'fa-square-parking', 'fa-road', 'fa-truck',
        ],
        'Rumah & Tagihan' => [
            'fa-house', 'fa-bolt', 'fa-tint', 'fa-faucet', 'fa-fire-flame-simple', 'fa-wifi', 'fa-mobile-alt',
            'fa-tv', 'fa-plug', 'fa-couch', 'fa-broom', 'fa-screwdriver-wrench', 'fa-key', 'fa-file-invoice-dollar',
        ],
        'Kesehatan & Keluarga' => [
            'fa-heartbeat', 'fa-pills', 'fa-stethoscope', 'fa-hospital', 'fa-tooth', 'fa-shield-heart',
            'fa-dumbbell', 'fa-spa', 'fa-baby', 'fa-baby-carriage', 'fa-paw',
        ],
        'Pendidikan & Pekerjaan' => [
            'fa-graduation-cap', 'fa-school', 'fa-book', 'fa-pen', 'fa-print', 'fa-laptop', 'fa-laptop-code',
            'fa-briefcase', 'fa-user-tie',
        ],
        'Hiburan & Liburan' => [
            'fa-film', 'fa-music', 'fa-gamepad', 'fa-ticket', 'fa-camera', 'fa-futbol', 'fa-person-swimming',
            'fa-umbrella-beach', 'fa-hotel',
        ],
        'Keuangan & Sosial' => [
            'fa-money-bill-wave', 'fa-wallet', 'fa-coins', 'fa-sack-dollar', 'fa-piggy-bank', 'fa-credit-card',
            'fa-building-columns', 'fa-money-bill-transfer', 'fa-hand-holding-dollar', 'fa-chart-line', 'fa-percent',
            'fa-gift', 'fa-hand-holding-heart', 'fa-mosque', 'fa-people-group',
        ],
        'Lainnya' => [
            'fa-plus-circle', 'fa-ellipsis-h', 'fa-star', 'fa-heart', 'fa-calendar-days',
        ],
    ];
@endphp

<div class="icon-picker border rounded p-2">
    @foreach($iconGroups as $group => $icons)
        <div class="icon-picker-group">{{ $group }}</div>
        <div class="icon-picker-grid">
            @foreach($icons as $icon)
                <button type="button" class="btn btn-outline-secondary icon-select-btn" data-icon="{{ $icon }}" title="{{ $icon }}">
                    <i class="fas {{ $icon }}"></i>
                </button>
            @endforeach
        </div>
    @endforeach
</div>
