<?php
namespace App\Support;

/** Data contoh agar view bisa dilihat tanpa backend. HAPUS saat controller asli siap. */
class Demo
{
    public static function settings() { return (object) ['name' => 'Cilok Sapi SR Khas Ciamis', 'wa' => '6281234567890', 'hours' => '08.00–21.00 setiap hari', 'address' => '[isi alamat outlet]', 'halal' => '', 'template' => 'Halo Cilok Sapi SR, saya mau pesan:']; }
    public static function user($role = 'owner') { return (object) ['name' => $role === 'owner' ? 'Pemilik' : 'Staf', 'role' => $role]; }
    public static function categories() { return collect(['Cilok', 'Tambahan', 'Minuman']); }
    public static function products()
    {
        return collect([[1, 'Cilok Sapi Original', 'Cilok', 10000, '🍢', 50], [2, 'Cilok Sapi Pedas', 'Cilok', 12000, '🌶️', 40], [3, 'Cilok Isi Keju', 'Cilok', 13000, '🧀', 30], [4, 'Kuah Kaldu Sapi', 'Tambahan', 3000, '🍲', 99], [5, 'Bumbu Kacang Extra', 'Tambahan', 2000, '🥜', 99], [6, 'Es Teh Manis', 'Minuman', 4000, '🧋', 99]])
            ->map(fn($r) => (object) ['id' => $r[0], 'name' => $r[1], 'category' => $r[2], 'price' => $r[3], 'emoji' => $r[4], 'stock' => $r[5], 'is_available' => true, 'is_active' => true, 'photo_url' => null, 'description' => [1 => 'Cilok sapi gurih dengan bumbu kacang khas.', 2 => 'Cilok sapi dengan sambal pedas racikan sendiri.', 3 => 'Cilok sapi berisi keju leleh.', 4 => 'Kuah kaldu sapi hangat pelengkap cilok.', 5 => 'Tambahan bumbu kacang untuk cilok.', 6 => 'Es teh manis segar.'][$r[0]]]);
    }
    public static function stock() { return collect([['id' => 1, 'name' => 'Daging sapi (kg)', 'qty' => 12], ['id' => 2, 'name' => 'Tepung tapioka (kg)', 'qty' => 20], ['id' => 3, 'name' => 'Bumbu kacang (kg)', 'qty' => 6]])->map(fn($r) => (object) $r); }
    public static function orders()
    {
        $mk = function ($id, $ch, $status, $items, $extra = []) {
            $it = collect($items)->map(fn($i) => (object) ['product_name_snapshot' => $i[0], 'unit_price_snapshot' => $i[1], 'qty' => $i[2], 'line_total' => $i[1] * $i[2]]);
            return (object) array_merge(['id' => $id, 'token' => 'tok' . $id, 'code' => 'CSR-' . date('Ymd') . '-' . str_pad($id, 3, '0', STR_PAD_LEFT), 'channel' => $ch,
                'customer_name' => $ch === 'online' ? 'Pembeli ' . $id : null, 'customer_phone' => $ch === 'online' ? '08120000' . $id : null, 'fulfillment' => 'ambil', 'pickup_time' => null, 'delivery_address' => null,
                'payment_method' => 'Tunai', 'payment_status' => 'paid', 'status' => $status, 'note' => null, 'created_at' => now(), 'items' => $it, 'total' => $it->sum('line_total'), 'statusLogs' => collect()], $extra);
        };
        return collect([
            $mk(1, 'online', 'Baru', [['Cilok Sapi Original', 10000, 2]], ['payment_status' => 'unpaid', 'payment_method' => 'Transfer', 'pickup_time' => '15:30']),
            $mk(2, 'offline', 'Selesai', [['Cilok Sapi Pedas', 12000, 3], ['Es Teh Manis', 4000, 1]]),
            $mk(3, 'online', 'Diproses', [['Cilok Isi Keju', 13000, 2]], ['fulfillment' => 'antar', 'delivery_address' => 'Jl. Contoh No. 1, Bandung', 'note' => 'Kuah dipisah']),
        ]);
    }
    public static function users() { return collect([(object) ['id' => 1, 'name' => 'Pemilik', 'email' => 'pemilik@ciloksapisr.test', 'role' => 'owner', 'is_active' => true], (object) ['id' => 2, 'name' => 'Staf 1', 'email' => 'staf1@ciloksapisr.test', 'role' => 'staff', 'is_active' => true], (object) ['id' => 3, 'name' => 'Staf 2', 'email' => 'staf2@ciloksapisr.test', 'role' => 'staff', 'is_active' => false]]); }
    public static function logs() { return collect([(object) ['created_at' => now(), 'user' => 'Pemilik', 'action' => 'Ubah harga Cilok Sapi Pedas: 11000 → 12000']]); }
}
