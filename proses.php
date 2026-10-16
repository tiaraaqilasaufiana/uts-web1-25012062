<?php
require_once __DIR__ . '/includes/fungsi.php';
require_once __DIR__ . '/data/menu.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('index.php');
}

$aksi = isset($_POST['aksi']) && is_string($_POST['aksi']) ? $_POST['aksi'] : '';

if ($aksi === 'tambah') {
    $kode = isset($_POST['kode']) && is_string($_POST['kode']) ? $_POST['kode'] : '';
    $jumlahRaw = $_POST['jumlah'] ?? null;

    if (!isset($menu[$kode])) {
        setFlash('Kode menu tidak dikenal. Keranjang tidak berubah.', 'error');
        redirect('index.php');
    }

    // Terima hanya representasi bilangan bulat; menolak kosong, pecahan, negatif, nol, dan >10.
    if (!is_string($jumlahRaw) && !is_int($jumlahRaw)) {
        setFlash('Jumlah harus berupa bilangan bulat 1 sampai 10.', 'error');
        redirect('index.php');
    }
    $jumlahTeks = (string)$jumlahRaw;
    if (!preg_match('/^\d+$/', $jumlahTeks)) {
        setFlash('Jumlah harus berupa bilangan bulat 1 sampai 10.', 'error');
        redirect('index.php');
    }
    $jumlah = (int)$jumlahTeks;
    if ($jumlah < 1 || $jumlah > 10) {
        setFlash('Jumlah harus berada di antara 1 sampai 10.', 'error');
        redirect('index.php');
    }

    if (!isset($_SESSION['keranjang'])) {
        $_SESSION['keranjang'] = [];
    }
    $jumlahLama = isset($_SESSION['keranjang'][$kode]) ? (int)$_SESSION['keranjang'][$kode]['jumlah'] : 0;
    if ($jumlahLama + $jumlah > 10) {
        setFlash('Penambahan ditolak. Jumlah setiap menu maksimal 10 dan keranjang tetap seperti sebelumnya.', 'error');
        redirect('keranjang.php');
    }

    $_SESSION['keranjang'][$kode] = ['jumlah' => $jumlahLama + $jumlah];
    setFlash($menu[$kode]['nama'] . ' berhasil ditambahkan ke keranjang.');
    redirect('keranjang.php');
}

if ($aksi === 'hapus') {
    $kode = isset($_POST['kode']) && is_string($_POST['kode']) ? $_POST['kode'] : '';
    if (isset($_SESSION['keranjang'][$kode])) {
        unset($_SESSION['keranjang'][$kode]);
        setFlash('Item berhasil dihapus dari keranjang.');
    } else {
        setFlash('Item tidak ditemukan di keranjang.', 'error');
    }
    redirect('keranjang.php');
}

if ($aksi === 'kosongkan') {
    $_SESSION['keranjang'] = [];
    setFlash('Keranjang berhasil dikosongkan.');
    redirect('keranjang.php');
}

setFlash('Aksi tidak dikenal.', 'error');
redirect('index.php');
