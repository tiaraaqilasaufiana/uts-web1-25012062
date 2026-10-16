<?php
require_once __DIR__ . '/includes/fungsi.php';
require_once __DIR__ . '/data/menu.php';

// Prioritas kategori: parameter GET yang valid, lalu cookie yang valid.
$kategoriDipilih = '';
if (isset($_GET['kategori']) && is_string($_GET['kategori']) && in_array($_GET['kategori'], $kategoriTersedia, true)) {
    $kategoriDipilih = $_GET['kategori'];
} elseif (isset($_COOKIE['kategori_favorit']) && in_array($_COOKIE['kategori_favorit'], $kategoriTersedia, true)) {
    $kategoriDipilih = $_COOKIE['kategori_favorit'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['aksi'] ?? '') === 'simpan_kategori') {
    $kategori = $_POST['kategori_favorit'] ?? '';
    if (is_string($kategori) && in_array($kategori, $kategoriTersedia, true)) {
        setcookie('kategori_favorit', $kategori, [
            'expires' => time() + (7 * 24 * 60 * 60),
            'path' => '/',
            'samesite' => 'Lax',
        ]);
        setFlash('Kategori favorit berhasil disimpan selama 7 hari.');
    } else {
        setFlash('Kategori favorit tidak valid.', 'error');
    }
    redirect('index.php');
}

$kataKunci = isset($_GET['cari']) && is_string($_GET['cari']) ? trim($_GET['cari']) : '';
$daftarTampil = [];
foreach ($menu as $kode => $item) {
    $cocokNama = $kataKunci === '' || stripos($item['nama'], $kataKunci) !== false;
    $cocokKategori = $kategoriDipilih === '' || $item['kategori'] === $kategoriDipilih;
    if ($cocokNama && $cocokKategori) {
        $daftarTampil[$kode] = $item;
    }
}

$judulHalaman = 'Daftar Menu';
require __DIR__ . '/includes/header.php';
?>
<section class="panel">
    <h2>Cari Menu</h2>
    <form method="get" action="index.php" class="search-form">
        <label for="cari">Nama menu</label>
        <input id="cari" type="text" name="cari" value="<?= e($kataKunci) ?>" placeholder="Contoh: nasi">
        <?php if ($kategoriDipilih !== ''): ?>
            <input type="hidden" name="kategori" value="<?= e($kategoriDipilih) ?>">
        <?php endif; ?>
        <button type="submit">Cari</button>
        <a class="button secondary" href="index.php">Reset</a>
    </form>
    <p class="muted">Kata kunci: <strong><?= e($kataKunci === '' ? 'Semua menu' : $kataKunci) ?></strong></p>
</section>

<section class="panel">
    <h2>Preferensi Kategori</h2>
    <form method="post" action="index.php" class="inline-form">
        <input type="hidden" name="aksi" value="simpan_kategori">
        <label for="kategori_favorit">Simpan kategori favorit (7 hari)</label>
        <select id="kategori_favorit" name="kategori_favorit" required>
            <?php foreach ($kategoriTersedia as $kategori): ?>
                <option value="<?= e($kategori) ?>" <?= $kategoriDipilih === $kategori ? 'selected' : '' ?>><?= e($kategori) ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit">Simpan Favorit</button>
    </form>
    <p class="muted">Kategori aktif: <?= e($kategoriDipilih === '' ? 'Semua kategori' : $kategoriDipilih) ?>. Parameter kategori GET yang valid mengalahkan cookie.</p>
    <?php if ($kategoriDipilih !== ''): ?><p><a href="index.php">Tampilkan semua kategori</a></p><?php endif; ?>
</section>

<section class="panel">
    <h2>Daftar Menu</h2>
    <?php if (!$daftarTampil): ?>
        <p class="alert error">Menu tidak ditemukan. Coba kata kunci atau kategori lain.</p>
    <?php else: ?>
        <div class="table-wrap">
        <table>
            <thead><tr><th>Kode</th><th>Nama Menu</th><th>Kategori</th><th>Harga</th><th>Pesan</th></tr></thead>
            <tbody>
            <?php foreach ($daftarTampil as $item): ?>
                <tr>
                    <td><?= e($item['kode']) ?></td>
                    <td><?= e($item['nama']) ?></td>
                    <td><?= e($item['kategori']) ?></td>
                    <td><?= e(rupiah($item['harga'])) ?></td>
                    <td>
                        <form method="post" action="proses.php" class="order-form">
                            <input type="hidden" name="aksi" value="tambah">
                            <input type="hidden" name="kode" value="<?= e($item['kode']) ?>">
                            <label class="sr-only" for="jumlah-<?= e($item['kode']) ?>">Jumlah <?= e($item['nama']) ?></label>
                            <input id="jumlah-<?= e($item['kode']) ?>" type="number" name="jumlah" min="1" max="10" step="1" placeholder="Jumlah" required>
                            <button type="submit">Tambah</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        </div>
    <?php endif; ?>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
