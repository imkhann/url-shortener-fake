<?php
// Ambil domain otomatis
$domain = $_SERVER['HTTP_HOST'];

// Ambil parameter dari URL
$url = $_GET['url'] ?? 'https://google.co.id';
$nama = $_GET['nama'] ?? 'Ambil Paket';
$tombol = $_GET['tombol'] ?? 'Cek Resi';

// Buat link asli
$fullUrl = "https://$domain/?url=" . urlencode($url) . "&nama=" . urlencode($nama) . "&tombol=" . urlencode($tombol);

// Fungsi shorten URL pakai TinyURL
function shortenURL($longUrl) {
    $api_url = "https://tinyurl.com/api-create.php?url=" . urlencode($longUrl);
    return file_get_contents($api_url) ?: $longUrl; // Jika gagal, tetap pakai URL asli
}

$shortUrl = '';

// Generate shorten URL jika ada input
if (!empty($_GET['url']) && !empty($_GET['nama']) && !empty($_GET['tombol'])) {
    $shortUrl = shortenURL($fullUrl);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate Link</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css">
</head>
<body class="bg-dark text-white">
    <div class="container mt-5">
        <div class="card shadow bg-secondary text-white">
            <div class="card-header bg-dark text-white text-center">
                <h4>Generate Link Custom</h4>
            </div>
            <div class="card-body">
                <form method="GET">
                    <div class="form-group">
                        <label>URL:</label>
                        <input type="url" class="form-control" name="url" value="<?= htmlspecialchars($url) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Nama:</label>
                        <input type="text" class="form-control" name="nama" value="<?= htmlspecialchars($nama) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Nama Tombol:</label>
                        <input type="text" class="form-control" name="tombol" value="<?= htmlspecialchars($tombol) ?>" required>
                    </div>
                    <button type="submit" class="btn btn-success btn-block mt-3">Generate</button>
                </form>

                <?php if (!empty($shortUrl)): ?>
                <div class="mt-4 p-3 bg-light text-dark rounded">
                    <p><strong>🔗 Link Asli:</strong> <a href="<?= htmlspecialchars($fullUrl) ?>" target="_blank"><?= htmlspecialchars($fullUrl) ?></a></p>
                    <p><strong>⚡ Shorten URL:</strong> <a href="<?= htmlspecialchars($shortUrl) ?>" target="_blank"><?= htmlspecialchars($shortUrl) ?></a></p>
                </div>
                <?php endif; ?>
            </div>
            <div class="card-footer text-center bg-dark text-white">
                <small>&copy; 2025 AlgazaDev</small>
            </div>
        </div>
    </div>
</body>
</html>