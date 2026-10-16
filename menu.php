<?php
// Data menu disimpan dalam array associative; tidak menggunakan database.
$menu = [
    'M001' => ['kode' => 'M001', 'nama' => 'Nasi Goreng',       'kategori' => 'Makanan', 'harga' => 25000],
    'M002' => ['kode' => 'M002', 'nama' => 'Mie Goreng',        'kategori' => 'Makanan', 'harga' => 24000],
    'M003' => ['kode' => 'M003', 'nama' => 'Roti Bakar',        'kategori' => 'Makanan', 'harga' => 15000],
    'M004' => ['kode' => 'M004', 'nama' => 'Es Teh',            'kategori' => 'Minuman', 'harga' => 5000],
    'M005' => ['kode' => 'M005', 'nama' => 'Jus Jeruk',         'kategori' => 'Minuman', 'harga' => 10000],
    'M006' => ['kode' => 'M006', 'nama' => 'Ayam Geprek',       'kategori' => 'Makanan', 'harga' => 30000],
    'M007' => ['kode' => 'M007', 'nama' => 'Kopi Susu',         'kategori' => 'Minuman', 'harga' => 18000],
];

$kategoriTersedia = array_values(array_unique(array_column($menu, 'kategori')));
