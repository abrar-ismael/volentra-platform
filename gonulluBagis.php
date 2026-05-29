<?php
// Oturumu başlat
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'baglanti.php';

$mesaj = '';
$kurumlar = [];
$gelen_kayitler = [];

// 1. GİRİŞ KONTROLÜ VE OTURUM KURTARMA
if (!isset($_SESSION['gonullu_ID']) && isset($_SESSION['gonullu_mail'])) {
    $mail = $_SESSION['gonullu_mail'];
    $sorgu = mysqli_query($baglanti, "SELECT gonullu_ID FROM gonulluBilgi WHERE gonullu_mail = '$mail'");
    if ($row = mysqli_fetch_assoc($sorgu)) {
        $_SESSION['gonullu_ID'] = $row['gonullu_ID'];
    }
}

$girisYaptiMi = isset($_SESSION['gonullu_ID']);
$oturumdakiID = $_SESSION['gonullu_ID'] ?? 0;

// 2. FORM İŞLEME (Randevu Al Butonu)
if (isset($_POST['randevuAl'])) {
    if (!$girisYaptiMi) {
        // Giriş yapmamışsa login sayfasına yönlendir
        header("Location: giris.php"); 
        exit();
    } else {
        $tarih = mysqli_real_escape_string($baglanti, $_POST['tarih']);
        $saat  = mysqli_real_escape_string($baglanti, $_POST['saat']);
        $secili_kurumlar = $_POST['kurumlar'] ?? [];

        if (!empty($tarih) && !empty($saat) && !empty($secili_kurumlar)) {
            // Randevu Ana Kaydı
            $sql1 = "INSERT INTO bagisRandevuBilgileri (gonulluID, bagisTarihi, bagisSaat, bagisBasvuruDurum) 
                     VALUES ('$oturumdakiID', '$tarih', '$saat', 'Beklemede')";

            if (mysqli_query($baglanti, $sql1)) {
                $yeniID = mysqli_insert_id($baglanti);
                
                // Ara tabloya (bagisBilgi) kurumları ekle
                foreach ($secili_kurumlar as $k_id) {
                    $k_id = (int)$k_id;
                    $sql2 = "INSERT INTO bagisBilgi (kurumID, randevuID) VALUES ('$k_id', '$yeniID')";
                    mysqli_query($baglanti, $sql2);
                }
                $mesaj = "<div class='alert alert-success fw-bold'>Randevu başarıyla alındı!</div>";
                header("Refresh:2");
            } else {
                $mesaj = "<div class='alert alert-danger'>Hata: " . mysqli_error($baglanti) . "</div>";
            }
        } else {
            $mesaj = "<div class='alert alert-warning'>Lütfen kurum, tarih ve saat seçiniz.</div>";
        }
    }
}

// 3. VERİLERİ ÇEKME (Her zaman çalışır)

// Kurum Listesini Çek (Select kutusu için)
$kurum_sorgu = mysqli_query($baglanti, "SELECT kurumID, kurum FROM bagisKurumBilgileri ORDER BY kurum ASC");
if ($kurum_sorgu) {
    while ($k = mysqli_fetch_assoc($kurum_sorgu)) { $kurumlar[] = $k; }
}

// Kullanıcının Geçmiş Başvurularını Çek (Tablo için)
if ($girisYaptiMi) {
    $liste_sorgu = "SELECT rb.*, GROUP_CONCAT(kb.kurum SEPARATOR ', ') as kurumlar 
                    FROM bagisRandevuBilgileri rb
                    LEFT JOIN bagisBilgi b ON rb.randevuID = b.randevuID
                    LEFT JOIN bagisKurumBilgileri kb ON b.kurumID = kb.kurumID
                    WHERE rb.gonulluID = '$oturumdakiID'
                    GROUP BY rb.randevuID
                    ORDER BY rb.bagisTarihi DESC, rb.bagisSaat DESC";
    
    $res = mysqli_query($baglanti, $liste_sorgu);
    if ($res) {
        while ($row = mysqli_fetch_assoc($res)) {
            $gelen_kayitler[] = $row;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Volentra - Bağış Randevusu</title>
  <link rel="icon" type="image/x-icon" href="img/volentra_favicon.png">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="style.css" />
</head>
<body>

  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg bg-ebony sticky-top" data-bs-theme="dark">
    <div class="container">
      <a class="navbar-brand d-flex align-items-center gap-2" href="#">
        <span class="volentra-logo" aria-hidden="true">
          <img src="img/volentra_logo.png" alt="Volentra" class="volentra-logo-img" />
        </span>
        <span class="volentra-brand-text">Volentra</span>
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#volentraNavbar">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="volentraNavbar">
        <ul class="navbar-nav ms-auto mb-2 mb-lg-0 gap-lg-2">
          <li class="nav-item"><a class="nav-link active" href="index.php">Anasayfa</a></li>
          <li class="nav-item"><a class="nav-link" href="gonulluBagis.php">Bağış Yap</a></li>
          <li class="nav-item"><a class="nav-link" href="etkinlik_gonullu_panel.php">Etkinlikler</a></li>
          <li class="nav-item"><a class="nav-link" href="#profil.php">Profil Bilgileri</a></li>
          <li class="nav-item"><a class="nav-link" href="cikis.php">Çıkış Yap</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Main -->
  <main class="py-4">
    <div class="container">
      <div class="row g-4 justify-content-center">
        <div class="col-12 col-lg-9">
          
          <!-- Form Card -->
          <section class="mb-4" id="bagis">
            <div class="card card-volentra shadow-sm border-0 rounded-4">
              <div class="card-body p-4">
                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2 mb-3">
                  <div>
                    <h1 class="h4 mb-1 text-ebony fw-bold">Bağış Randevusu Oluştur</h1>
                    <p class="text-muted mb-0">Uygun bir kurum ve tarih/saat seçerek randevunuzu oluşturun.</p>
                  </div>
                  <div class="volentra-badge-pill">Kolay • Hızlı • Güvenli</div>
                </div>

                <!-- Mesaj Alanı -->
                <?php if(!empty($mesaj)) { echo "<div class='mb-3'>$mesaj</div>"; } ?>
                
                <!-- Geliştirme: Formun action kısmına mevcut sayfa ismi ve butona name eklendi -->
                <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                  <div class="row g-3">

                    <div class="col-12 col-md-6">
                      <label class="form-label fw-semibold" id="kurumSecimiLabel">Kurum Seçimi</label>
                      <div class="border rounded-4 p-3 bg-white bg-opacity-50" role="group" aria-labelledby="kurumSecimiLabel">
                        <div class="d-flex flex-column gap-2" id="kurumSecimi">
                          <?php if (!empty($kurumlar)) { foreach ($kurumlar as $k) { ?>
                            <div class="form-check">
                              <input class="form-check-input" type="checkbox" id="kurum-<?= (int)$k['kurumID']; ?>" name="kurumlar[]" value="<?= (int)$k['kurumID']; ?>" />
                              <label class="form-check-label" for="kurum-<?= (int)$k['kurumID']; ?>"><?= htmlspecialchars($k['kurum'], ENT_QUOTES, 'UTF-8'); ?></label>
                            </div>
                          <?php } } else { ?>
                            <div class="text-muted">Kayıtlı kurum bulunamadı.</div>
                          <?php } ?>
                        </div>
                        <div class="form-text mt-2">Birden fazla kurum seçebilirsiniz.</div>
                      </div>
                    </div>

                    <div class="col-12 col-md-6">
                      <label for="randevuTarihi" class="form-label fw-semibold">Tarih</label>
                      <input id="randevuTarihi" name="tarih" type="date" class="form-control form-control-volentra" required />
                    </div>

                    <div class="col-12 col-md-6">
                      <label for="randevuSaati" class="form-label fw-semibold">Saat</label>
                      <input id="randevuSaati" name="saat" type="time" class="form-control form-control-volentra" required />
                    </div>

                    <div class="col-12 d-grid d-md-flex align-items-center gap-2 mt-2">
                      <!-- KRİTİK DEĞİŞİKLİK: Butona name="randevuAl" eklendi -->
                      <button type="submit" name="randevuAl" class="btn btn-volentra btn-lg rounded-3">
                        Randevu Al
                      </button>
                      <div class="text-muted small">
                        Onay süreci için e-posta/telefon bilgilendirmesi kullanılır.
                      </div>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </section>

          <!-- Table Card -->
          <section aria-label="Randevu Takip" class="mb-5">
            <div class="card card-volentra shadow-sm border-0 rounded-4">
              <div class="card-body p-4">
                <div class="mb-3">
                  <h2 class="h5 mb-1 text-ebony fw-bold">Randevu Takip</h2>
                  <p class="text-muted mb-0">Geçmiş bağış randevularınızın durumu.</p>
                </div>

                <div class="table-responsive">
                  <table class="table table-hover align-middle mb-0">
                    <thead>
                      <tr class="bg-soft">
                        <th scope="col">Kurum Adı</th>
                        <th scope="col">Randevu Tarihi</th>
                        <th scope="col">Saat</th>
                        <th scope="col">Randevu Durumu</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php if (!empty($gelen_kayitler)) { foreach ($gelen_kayitler as $r) {
                        $durumText = $r['bagisBasvuruDurum'] ?? '';
                        $badgeClass = ($durumText == 'Onaylandı') ? 'status-approved' : (($durumText == 'Tamamlandı') ? 'status-completed' : 'status-pending');
                      ?>
                        <tr>
                          <td class="fw-semibold"><?= htmlspecialchars($r['kurumlar'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                          <td><?= htmlspecialchars($r['bagisTarihi'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                          <td><?= htmlspecialchars($r['bagisSaat'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                          <td>
                            <span class="badge <?= $badgeClass; ?>"><?= htmlspecialchars($durumText, ENT_QUOTES, 'UTF-8'); ?></span>
                          </td>
                        </tr>
                      <?php } } else { ?>
                        <tr><td colspan="4" class="text-center py-4 text-muted">Henüz bir bağış başvurunuz bulunmuyor ya da Kullanıcı ID girmediniz.</td></tr>
                      <?php } ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </section>

        </div>
      </div>
    </div>
  </main>

  <!-- Footer -->
  <footer class="bg-ebony text-white mt-auto py-4">
    <div class="container text-center text-md-start">
      <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2">
        <div class="fw-semibold">Volentra &copy; 2026</div>
        <div class="text-white-50">Gönüllülük ve sosyal yardımlaşma platformu.</div>
      </div>
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>