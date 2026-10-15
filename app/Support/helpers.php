<?php
// Daftarkan di composer.json -> "autoload": {"files": ["app/Support/helpers.php"]} lalu jalankan: composer dump-autoload
if (!function_exists('rupiah')) {
    function rupiah($n): string { return 'Rp' . number_format((int) $n, 0, ',', '.'); }
}
if (!function_exists('order_statuses')) {
    function order_statuses(): array { return ['Baru', 'Dikonfirmasi', 'Diproses', 'Siap/Dikirim', 'Selesai']; }
}
if (!function_exists('status_label')) {
    function status_label(string $s, ?string $fulfillment = 'ambil'): string {
        return $s === 'Siap/Dikirim' ? ($fulfillment === 'antar' ? 'Dalam pengantaran' : 'Siap diambil') : $s;
    }
}
if (!function_exists('next_status')) {
    function next_status(string $s): ?string {
        $i = array_search($s, order_statuses(), true);
        return $i === false || $i >= 4 ? null : order_statuses()[$i + 1];
    }
}
if (!function_exists('mask_phone')) {
    function mask_phone(?string $p): string {
        return !$p || strlen($p) < 8 ? ($p ?: '-') : substr($p, 0, 4) . '••••' . substr($p, -3);
    }
}
