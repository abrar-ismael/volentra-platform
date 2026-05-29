<?php
session_start(); // Oturum yönetimini başlatıyoruz

// 1. Veritabanı Bağlantısı
$baglanti = mysqli_connect('localhost', 'volentra_vol', 'Volentra.11', 'volentra_volentra');

if (!$baglanti) {
    die("Bağlantı hatası: " . mysqli_connect_error());
}
mysqli_set_charset($baglanti, "utf8");

/**
 * GİRİŞ YAPAN KULLANICIYI BELİRLEME
 * gonulluGiris tablosundan mail ile giriş yapıldığında 
 * $_SESSION['gonullu_mail'] değişkeninin dolu olduğu varsayılır.
 */
$oturum_mail = $_SESSION['gonullu_mail'] ?? 'ornek@mail.com'; // Test için varsayılan mail

// Mail adresi üzerinden gonulluBilgi tablosundan gonullu_ID ve diğer verileri çekiyoruz
$kullanici_sorgu = mysqli_query($baglanti, "SELECT * FROM gonulluBilgi WHERE gonullu_mail = '$oturum_mail'");
$kullanici_verisi = mysqli_fetch_assoc($kullanici_sorgu);

// Giriş yapan gönüllünün ID'sini otomatik alıyoruz
$giris_yapan_id = $kullanici_verisi['gonullu_ID'] ?? 0; 

$mesaj = "";

// 2. Başvuru İşlemi (Butona tıklandığında)
if (isset($_GET['etkinlik_id']) && $giris_yapan_id > 0) {
    $etkinlik_id = mysqli_real_escape_string($baglanti, $_GET['etkinlik_id']);
    $tarih = date('Y-m-d');
    $durum = "Beklemede"; // Varsayılan durum

    // Aynı kişinin aynı etkinliğe mükerrer başvurusunu kontrol et
    $kontrol = mysqli_query($baglanti, "SELECT * FROM etkinlikBasvuruBilgi WHERE etkinlik_ID = '$etkinlik_id' AND gonullu_ID = '$giris_yapan_id'");
    
    if (mysqli_num_rows($kontrol) > 0) {
        $mesaj = "<div class='alert alert-warning alert-dismissible fade show' role='alert'>
                    Bu etkinliğe zaten başvurunuz bulunuyor.
                    <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Kapat'></button>
                  </div>";
    } else {
        // SQL Sorgusu - Veritabanı tarafından basvuru_ID otomatik atanır
        $sorgu = "INSERT INTO etkinlikBasvuruBilgi (etkinlik_ID, gonullu_ID, basvuru_tarih, basvuru_durum) 
                  VALUES ('$etkinlik_id', '$giris_yapan_id', '$tarih', '$durum')";
        
        if (mysqli_query($baglanti, $sorgu)) {
            $mesaj = "<div class='alert alert-success alert-dismissible fade show' role='alert'>
                        Başvurunuz başarıyla alındı! Durum: Beklemede.
                        <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Kapat'></button>
                      </div>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Volentra - Etkinlik</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@3.19.0/tabler-icons.min.css" />
  <link rel="stylesheet" href="style.css" />
  <link rel="icon" type="image/x-icon" href="img/volentra_favicon.png">
</head>
<body class="d-flex flex-column min-vh-100">

  <nav class="navbar navbar-expand-lg bg-ebony sticky-top" data-bs-theme="dark">
    <div class="container">
      <a class="navbar-brand d-flex align-items-center gap-2" href="#">
        <span class="volentra-logo" aria-hidden="true">
          <img src="img/volentra_logo.png" alt="Volentra" class="volentra-logo-img" />
        </span>
        <span class="volentra-brand-text">Volentra</span>
      </a>
      <div class="collapse navbar-collapse" id="volentraNavbar">
        <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-lg-center gap-lg-2">
          <li class="nav-item"><a class="nav-link" href="#">Anasayfa</a></li>
          <li class="nav-item"><a class="nav-link" href="gonulluBagis.html">Bağış Yap</a></li>
          <li class="nav-item"><a class="nav-link active" href="etkinlik_gonullu_panel.html">Etkinlikler</a></li>
          <li class="nav-item"><a class="nav-link" href="#profil">Profil Bilgileri</a></li>
          <li class="nav-item"><a class="nav-link" href="#giris">Çıkış Yap</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <main class="py-5 flex-grow-1">
    <div class="container">
      
      <!-- Kullanıcı Selamlama (Otomatik Veri Çekme Kanıtı) -->
      <div class="mb-4">
          <p class="text-secondary small mb-0">Hoş geldin, <strong><?php echo ($kullanici_verisi['gonullu_ad'] ?? 'Gönüllü') . ' ' . ($kullanici_verisi['gonullu_soyad'] ?? ''); ?></strong></p>
          <p class="text-muted extra-small">ID: <?php echo $giris_yapan_id; ?> | E-posta: <?php echo $oturum_mail; ?></p>
      </div>

      <?php echo $mesaj; ?>

      <h2 class="h4 fw-bold mb-4 text-ebony border-start border-4 border-success ps-3">Etkinlikler</h2>

      <div class="row g-4 mb-5">
        
        <!-- Kart 1 -->
        <div class="col-md-4 d-flex">
          <div class="card card-volentra border-0 rounded-4 shadow-sm w-100 d-flex flex-column justify-content-between p-4">
            <div>
              <div class="d-flex justify-content-between align-items-center mb-3">
                <span>Sağlık</span>
                <small class="text-muted fw-semibold"><i class="ti ti-users me-1"></i>25/50 Dolu</small>
              </div>
              <h3 class="h5 fw-bold text-ebony mb-2">Kan Bağışı Kampanyası</h3>
              <p class="text-muted fs-7 mb-3">Kızılay iş birliğiyle düzenlenen bu etkinlikte hayat kurtarın.</p>
            </div>
            <div>
              <a href="?etkinlik_id=101" class="btn btn-volentra w-100 py-2">Başvur</a>
            </div>
          </div>
        </div>

        <!-- Kart 2 -->
        <div class="col-md-4 d-flex">
          <div class="card card-volentra border-0 rounded-4 shadow-sm w-100 d-flex flex-column justify-content-between p-4">
            <div>
              <div class="d-flex justify-content-between align-items-center mb-3">
                <span>Eğitim</span>
                <small class="text-muted fw-semibold"><i class="ti ti-users me-1"></i>12/30 Dolu</small>
              </div>
              <h3 class="h5 fw-bold text-ebony mb-2">Köy Okulu Kütüphanesi</h3>
              <p class="text-muted fs-7 mb-3">İhtiyacı olan okulumuz için raflara kitap dizecek gönüllüler arıyoruz.</p>
            </div>
            <div>
              <a href="?etkinlik_id=102" class="btn btn-volentra w-100 py-2">Başvur</a>
            </div>
          </div>
        </div>

      </div>
    
      <!-- TABLO: Giriş Yapan Gönüllünün Başvuruları -->
      <section class="card card-volentra border-0 rounded-4 shadow-sm">
        <div class="card-body p-4">
          <h2 class="h5 fw-bold mb-4 text-ebony border-start border-4 border-warning ps-3">Başvurularım</h2>
          <div class="table-responsive">
            <table class="table table-hover align-middle">
              <thead class="bg-soft">
                <tr>
                  <th>Etkinlik ID</th>
                  <th>Tarih</th>
                  <th>Başvuru Durumu</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $liste = mysqli_query($baglanti, "SELECT * FROM etkinlikBasvuruBilgi WHERE gonullu_ID = '$giris_yapan_id'");
                if(mysqli_num_rows($liste) > 0) {
                    while ($s = mysqli_fetch_assoc($liste)) {
                        $badge_class = ($s['basvuru_durum'] == "Onaylandı") ? "bg-success-subtle text-success" : "status-pending";
                        echo "<tr>";
                        echo "<td class='fw-bold'>Etkinlik #{$s['etkinlik_ID']}</td>";
                        echo "<td>{$s['basvuru_tarih']}</td>";
                        echo "<td><span class='badge {$badge_class} px-3 py-2 rounded-3'>{$s['basvuru_durum']}</span></td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='3' class='text-center py-4 text-muted'>Henüz bir başvurunuz bulunmuyor.</td></tr>";
                }
                ?>
              </tbody>
            </table>
          </div>
        </div>
      </section>
    </div>
  </main>

  <footer class="bg-ebony text-white mt-auto py-4 text-center">
      <div class="fw-semibold">Volentra &copy; 2026</div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>