<?php
session_start();

// 1. Veritabanı Bağlantısı
$baglanti = mysqli_connect('localhost', 'volentra_vol', 'Volentra.11', 'volentra_volentra');

if (!$baglanti) {
    die("Bağlantı hatası: " . mysqli_connect_error());
}
mysqli_set_charset($baglanti, "utf8");

$mesaj = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Formdan gelen verileri güvenli hale getiriyoruz
    $ad      = mysqli_real_escape_string($baglanti, $_POST['ad']);
    $soyad   = mysqli_real_escape_string($baglanti, $_POST['soyad']);
    $cinsiyet= mysqli_real_escape_string($baglanti, $_POST['cinsiyet']);
    $yas     = mysqli_real_escape_string($baglanti, $_POST['yas']);
    $sehir   = mysqli_real_escape_string($baglanti, $_POST['sehir']);
    $egitim  = mysqli_real_escape_string($baglanti, $_POST['egitim']);
    $telefon = mysqli_real_escape_string($baglanti, $_POST['telefon']);
    $mail    = mysqli_real_escape_string($baglanti, $_POST['mail']);
    
    // Şifreleri doğrulamak için önce ham hallerini alıyoruz
    $saf_sifre  = $_POST['sifre'];
    $saf_sifre2 = $_POST['sifre_tekrar'];

    if ($saf_sifre !== $saf_sifre2) {
        $mesaj = "<div class='alert alert-danger'>Şifreler uyuşmuyor!</div>";
    } else {
        // Şifreler uyuşuyorsa veritabanı için güvenli hale getiriyoruz
        $sifre = mysqli_real_escape_string($baglanti, $saf_sifre);

        // Mail adresi kontrolü
        $kontrol = mysqli_query($baglanti, "SELECT * FROM gonulluGiris WHERE gonullu_mail = '$mail'");
        
        if (mysqli_num_rows($kontrol) > 0) {
            $mesaj = "<div class='alert alert-warning'>Bu e-posta adresi zaten kayıtlı.</div>";
        } else {
            // Önce profil bilgilerini gonulluBilgi tablosuna kaydediyoruz
            $sorguBilgi = "INSERT INTO gonulluBilgi (gonullu_ad, gonullu_soyad, gonullu_cinsiyet, gonullu_yas, gonullu_sehir, gonullu_egitim, gonullu_tel, gonullu_mail) 
                           VALUES ('$ad', '$soyad', '$cinsiyet', '$yas', '$sehir', '$egitim', '$telefon', '$mail')";
            
            if (mysqli_query($baglanti, $sorguBilgi)) {
                // Sonra giriş bilgilerini gonulluGiris tablosuna kaydediyoruz
                $sorguGiris = "INSERT INTO gonulluGiris (gonullu_mail, gonullu_sifre) 
                               VALUES ('$mail', '$sifre')";
                
                if (mysqli_query($baglanti, $sorguGiris)) {
                    $mesaj = "<div class='alert alert-success'>Kaydınız başarıyla oluşturuldu! Giriş sayfasına yönlendiriliyorsunuz...</div>";
                    // 3 saniye sonra giriş sayfasına (giris.php) yönlendirir
                    header("Refresh: 3; url=giris.php"); 
                } else {
                    $mesaj = "<div class='alert alert-danger'>Giriş kaydı hatası: " . mysqli_error($baglanti) . "</div>";
                }
            } else {
                $mesaj = "<div class='alert alert-danger'>Bilgi kaydı hatası: " . mysqli_error($baglanti) . "</div>";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kayıt Ol - Volentra</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@3.19.0/tabler-icons.min.css" />
    <link rel="stylesheet" href="style.css" />
</head>
<body class="d-flex flex-column min-vh-100">

    <nav class="navbar navbar-expand-lg bg-ebony sticky-top" data-bs-theme="dark">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="index.html">
                <img src="img/volentra_logo.png" alt="Volentra" class="volentra-logo-img" />
                <span class="volentra-brand-text">Volentra</span>
            </a>
        </div>
    </nav>

    <main class="flex-grow-1 d-flex align-items-center justify-content-center py-5">
        <div class="card card-volentra border-0 shadow" style="max-width: 700px; border-radius: 1.5rem;">
            <div class="card-body p-4 p-md-5">
                <div class="text-center mb-4">
                    <i class="ti ti-user-plus fs-1" style="color: var(--dusty-olive);"></i>
                    <h2 class="fw-bold">Kayıt Ol</h2>
                    <p class="text-muted">Gönüllü topluluğumuza katılmak için formu doldur.</p>
                </div>

                <!-- PHP Mesaj Alanı -->
               <?php echo $mesaj; ?>
                <!-- Form başlangıcını bu şekilde değiştiriyoruz -->
<form method="POST" action=""> 
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label fw-semibold">Ad</label>
            <input type="text" name="ad" class="form-control form-control-volentra" placeholder="Adınız" required>
        </div>
        <div class="col-md-6">
            <label class="form-label fw-semibold">Soyad</label>
            <input type="text" name="soyad" class="form-control form-control-volentra" placeholder="Soyadınız" required>
        </div>
        <div class="col-md-6">
            <label class="form-label fw-semibold">Cinsiyet</label>
            <select name="cinsiyet" class="form-select form-select-volentra" required>
                <option value="" disabled selected>Seçiniz</option>
                <option value="Erkek">Erkek</option>
                <option value="Kadın">Kadın</option>
                <option value="Belirtmek İstemiyorum">Belirtmek İstemiyorum</option>
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label fw-semibold">Yaş</label>
            <input type="number" name="yas" class="form-control form-control-volentra text-start" min="15" placeholder="Yaşınız" required>
        </div>
        <div class="col-md-6">
            <label class="form-label fw-semibold">Şehir</label>
            <input type="text" name="sehir" class="form-control form-control-volentra" placeholder="Şehir" required>
        </div>
        <div class="col-md-6">
    <label class="form-label fw-semibold">Eğitim Durumu</label>
    <select name="egitim" class="form-select form-select-volentra" required>
        <option value="" disabled selected>Seçiniz</option>
        <option value="Lise">Lise</option>
        <option value="Önlisans">Önlisans</option>
        <option value="Lisans">Lisans</option>
        <option value="Doktora">Doktora</option>
</select>
</div>
        <div class="col-md-6">
            <label class="form-label fw-semibold">Telefon</label>
            <input type="tel" name="telefon" class="form-control form-control-volentra" placeholder="05xx xxx xx xx" required>
        </div>
        <div class="col-md-12">
            <label class="form-label fw-semibold">E-posta</label>
            <input type="email" name="mail" class="form-control form-control-volentra" placeholder="ornek@mail.com" required>
        </div>
        <div class="col-md-6">
            <label class="form-label fw-semibold">Şifre</label>
            <input type="password" name="sifre" class="form-control form-control-volentra" placeholder="••••••••" required>
        </div>
        <div class="col-md-6">
            <label class="form-label fw-semibold">Şifre Tekrar</label>
            <input type="password" name="sifre_tekrar" class="form-control form-control-volentra" placeholder="••••••••" required>
        </div>
        <div class="col-12 mt-4">
            <button type="submit" class="btn btn-volentra w-100 py-2 rounded-pill">Gönüllü Olarak Kaydol</button>
        </div>
    </div>
</form>

                <div class="text-center mt-4">
                    <p class="small text-muted">
                        Zaten bir hesabın var mı? 
                        <a href="giris.php" class="fw-bold text-decoration-none" style="color: var(--ebony);">Giriş Yap</a>
                    </p>
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