<?php
session_start();

// 1. Veritabanı Bağlantısı
$baglanti = mysqli_connect('localhost', 'volentra_vol', 'Volentra.11', 'volentra_volentra');

if (!$baglanti) {
    die("Bağlantı hatası: " . mysqli_connect_error());
}
mysqli_set_charset($baglanti, "utf8");

$hata_mesaji = "";

// 2. Giriş İşlemi
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($baglanti, $_POST['email']);
    $sifre = mysqli_real_escape_string($baglanti, $_POST['sifre']);
    $yetki = $_POST['yetki'];

    // Güvenlik: Şifreleme kullanıyorsan md5 veya password_verify eklemelisin. 
    // Şimdilik düz metin kontrolü yapıyoruz (İlk kodundaki mantığa göre).
    
    if ($yetki == "volunteer") {
        $sorgu = "SELECT * FROM gonulluGiris WHERE gonullu_mail = '$email' AND gonullu_sifre = '$sifre'";
        $sonuc = mysqli_query($baglanti, $sorgu);
        
        if (mysqli_num_rows($sonuc) > 0) {
            $kullanici = mysqli_fetch_assoc($sonuc);
            $_SESSION['gonullu_mail'] = $kullanici['gonullu_mail'];
            $_SESSION['yetki'] = 'volunteer';
            header("Location: index.php"); // Başarılı girişte yönlendirilecek sayfa
            exit();
        } else {
            $hata_mesaji = "E-posta veya şifre hatalı!";
        }
    }
    elseif ($yetki == "admin") {
        $sorgu = "SELECT * FROM yoneticiGiris WHERE yMail = '$email' AND ySifre = '$sifre'";
        $sonuc = mysqli_query($baglanti, $sorgu);
        
        if (mysqli_num_rows($sonuc) > 0) {
            $kullanici = mysqli_fetch_assoc($sonuc);
            $_SESSION['yMail'] = $kullanici['yMail'];
            $_SESSION['yetki'] = 'admin';
            header("Location: etkinlik_yonetici_panel.php");
            exit();
        } else {
            $hata_mesaji = "E-posta veya şifre hatalı!";
        }

    } else {
        $hata_mesaji = "Geçersiz yetki seçimi.";
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giriş Yap - Volentra</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@3.19.0/tabler-icons.min.css" />
    <link rel="stylesheet" href="style.css" />
</head>
<body class="d-flex flex-column min-vh-100">

    <nav class="navbar bg-ebony sticky-top" data-bs-theme="dark">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="index.php">
                <img src="img/volentra_logo.png" alt="Volentra" class="volentra-logo-img" />
                <span class="volentra-brand-text">Volentra</span>
            </a>
        </div>
    </nav>

    <main class="flex-grow-1 d-flex align-items-center justify-content-center py-5">
        <div class="card card-volentra border-0 shadow" style="width: 100%; max-width: 400px; border-radius: 1.5rem;">
            <div class="card-body p-4 p-md-5 text-center">
                <i class="ti ti-user-circle fs-1" style="color: var(--ebony);"></i>
                <h2 class="fw-bold mt-3">Giriş Yap</h2>
                
                <!-- Hata Mesajı Alanı -->
                <?php if ($hata_mesaji != ""): ?>
                    <div class="alert alert-danger mt-3 small py-2"><?php echo $hata_mesaji; ?></div>
                <?php endif; ?>
                
                <form id="loginForm" class="mt-4 text-start" method="POST" action="">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">E-posta Adresi</label>
                        <input type="email" name="email" class="form-control form-control-volentra" placeholder="ornek@mail.com" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Şifre</label>
                        <input type="password" name="sifre" class="form-control form-control-volentra" placeholder="••••••••" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Giriş Yetkisi</label>
                        <select name="yetki" class="form-select form-select-volentra" required>
                            <option value="volunteer">Gönüllü</option>
                            <option value="admin">Yönetici</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-volentra w-100 py-2 rounded-pill">Giriş Yap</button>
                </form>
                
                <div class="mt-4">
                    <span class="text-muted small">Henüz hesabın yok mu?</span><br>
                    <a href="KayitSayfasi.php" class="fw-bold text-decoration-none" style="color: var(--coffee-bean);">Kayıt Ol</a>
                </div>
            </div>
        </div>
    </main>

   <footer class="bg-ebony text-white mt-auto py-4">
    <div class="container">
      <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2">
        <div class="fw-semibold">Volentra &copy; 2026</div>
        <div class="text-white-50">Gönüllülük ve sosyal yardımlaşma platformu.</div>
      </div>
    </div>
  </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>