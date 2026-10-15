<?php
// ROUTE DEMO: closure + data contoh supaya semua view bisa dibuka tanpa backend.
// Saat backend siap, ganti closure dengan controller (nama route JANGAN diubah, dipakai di view).
use App\Support\Demo;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

$demoBack = fn() => back()->with('ok', 'Contoh: aksi diterima (belum terhubung backend).');

// ---------- Publik ----------
Route::get('/', fn() => view('public.home', ['settings' => Demo::settings(), 'categories' => Demo::categories(), 'products' => Demo::products()->where('is_active', true)]))->name('home');
Route::get('/keranjang', fn() => view('public.cart', ['settings' => Demo::settings(), 'products' => Demo::products()]))->name('cart');
Route::get('/checkout', fn() => view('public.checkout', ['settings' => Demo::settings(), 'products' => Demo::products()]))->name('checkout');
Route::post('/checkout', fn() => redirect()->route('order.show', 'tok1'))->name('checkout.store');
Route::get('/pesanan/{token}', function ($token) {
    $s = Demo::settings(); $o = Demo::orders()->first();
    $msg = $s->template . "\n" . $o->items->map(fn($i) => "- {$i->product_name_snapshot} x{$i->qty} = " . rupiah($i->line_total))->implode("\n") . "\nTotal: " . rupiah($o->total) . ($o->fulfillment === 'ambil' && $o->pickup_time ? "\nWaktu ambil: {$o->pickup_time}" : '') . "\nKode: {$o->code}";
    return view('public.confirm', ['settings' => $s, 'order' => $o, 'message' => $msg, 'waUrl' => 'https://wa.me/' . $s->wa . '?text=' . rawurlencode($msg)]);
})->name('order.show');

// ---------- Auth ----------
Route::get('/masuk', [AuthController::class, 'form'])->name('login');
Route::post('/masuk', [AuthController::class, 'login'])->middleware('throttle:5,1')->name('login.store');
// Verifikasi dua langkah untuk Pemilik (Fase 2, A07). Backend: arahkan Pemilik ke sini setelah kata sandi benar.
Route::get('/masuk/verifikasi', fn() => view('admin.two-factor'))->name('login.2fa');
Route::post('/masuk/verifikasi', fn() => redirect()->route('admin.orders'))->name('login.2fa.verify');
Route::post('/keluar', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// ---------- Admin (tambahkan middleware auth + role di backend) ----------
Route::prefix('admin')->middleware(['auth', 'dashboard.role'])->group(function () use ($demoBack) {

    Route::get('/pesanan', fn() => view('admin.orders.index', ['orders' => Demo::orders(), 'filters' => request()->only('status', 'date', 'channel')]))->name('admin.orders');
    Route::get('/pesanan/{order}', fn($id) => view('admin.orders.show', ['order' => Demo::orders()->firstWhere('id', (int) $id) ?? Demo::orders()->first()]))->name('admin.orders.show');
    Route::post('/pesanan/{order}/status', $demoBack)->name('admin.orders.status');
    Route::post('/pesanan/{order}/bayar', $demoBack)->name('admin.orders.payment');
    Route::post('/pesanan/{order}/anonimkan', $demoBack)->name('admin.orders.anonymize');

    Route::get('/offline', fn() => view('admin.offline', ['products' => Demo::products()->where('is_active', true)]))->name('admin.offline');
    Route::post('/offline', $demoBack)->name('admin.offline.store');

    Route::get('/produk', fn() => view('admin.products.index', ['products' => Demo::products()]))->name('admin.products');
    Route::get('/produk/baru', fn() => view('admin.products.create', ['categories' => Demo::categories()]))->name('admin.products.create');
    Route::post('/produk', $demoBack)->name('admin.products.store');
    Route::get('/produk/{product}/ubah', fn($id) => view('admin.products.edit', ['categories' => Demo::categories(), 'product' => Demo::products()->firstWhere('id', (int) $id)]))->name('admin.products.edit');
    Route::put('/produk/{product}', $demoBack)->name('admin.products.update');
    Route::patch('/produk/{product}/toggle', $demoBack)->name('admin.products.toggle');

    Route::get('/laporan', function () {
        $done = Demo::orders()->where('status', 'Selesai');
        $rows = $done->flatMap->items->groupBy('product_name_snapshot')->map(fn($g, $n) => (object) ['name' => $n, 'qty' => $g->sum('qty'), 'total' => $g->sum('line_total')])->values();
        return view('admin.report', ['mode' => request('mode', 'day'), 'date' => request('date', now()->toDateString()), 'rows' => $rows, 'max' => max(1, $rows->max('total')),
            'summary' => (object) ['count' => $done->count(), 'total' => $done->sum('total'), 'online' => $done->where('channel', 'online')->sum('total'), 'offline' => $done->where('channel', 'offline')->sum('total')]]);
    })->middleware('role:owner')->name('admin.report');
    Route::get('/stok', fn() => view('admin.stock', ['items' => Demo::stock()]))->name('admin.stock');
    Route::post('/stok/{item}', $demoBack)->name('admin.stock.adjust');
    Route::get('/pengaturan', fn() => view('admin.settings', ['settings' => Demo::settings()]))->middleware('role:owner')->name('admin.settings');
    Route::put('/pengaturan', $demoBack)->middleware('role:owner')->name('admin.settings.update');
    Route::get('/log', fn() => view('admin.logs', ['logs' => Demo::logs()]))->name('admin.logs');
});
