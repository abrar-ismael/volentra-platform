<?php
session_start();

$baglanti = mysqli_connect('localhost', 'volentra_vol', 'Volentra.11', 'volentra_volentra');

if (!$baglanti) {
    die("Bağlantı hatası: " . mysqli_connect_error());
}
mysqli_set_charset($baglanti, "utf8mb4");

// Sadece admin girebilir
if (!isset($_SESSION['yetki']) || $_SESSION['yetki'] !== 'admin') {
    header("Location: giris.php");
    exit();
}
$yonetici_mail = $_SESSION['yMail'];

$yonetici_sorgu = mysqli_query($baglanti, "SELECT yID FROM yoneticiBilgi WHERE yMail = '$yonetici_mail' LIMIT 1");
$yonetici_verisi = mysqli_fetch_assoc($yonetici_sorgu);
$yonetici_id = $yonetici_verisi['yID'];

// Başvuru onayla/reddet işlemi
if (isset($_GET['karar']) && isset($_GET['basvuru_id'])) {
    $basvuru_id = (int) $_GET['basvuru_id'];
    $karar = $_GET['karar'] === 'onayla' ? 'Onaylandı' : 'Reddedildi';

    mysqli_query($baglanti, "UPDATE etkinlikBasvuruBilgi SET basvuru_durum = '$karar' WHERE basvuru_ID = '$basvuru_id'");
    
    header("Location: etkinlik_yonetici_panel.php");
    exit();
}


// Yeni etkinlik ekle

// Etkinlik sil
if (isset($_GET['sil_id'])) {
    $sil_id = (int) $_GET['sil_id'];
    mysqli_query($baglanti, "DELETE FROM etkinlikBilgi WHERE etkinlikID = '$sil_id'");
    $_SESSION['mesaj']     = "Etkinlik silindi.";
    $_SESSION['mesaj_tip'] = "danger";
    header("Location: etkinlik_yonetici_panel.php");
    exit();
}

// Etkinlik güncelleme — GET ile form doldur
$guncelle_verisi = null;
if (isset($_GET['guncelle_id'])) {
    $guncelle_id = (int) $_GET['guncelle_id'];
    $g = mysqli_query($baglanti, "SELECT * FROM etkinlikBilgi WHERE etkinlikID = '$guncelle_id' LIMIT 1");
    $guncelle_verisi = mysqli_fetch_assoc($g);
}

// Etkinlik güncelleme — POST ile kaydet
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guncelle_id'])) {
    $gid      = (int) $_POST['guncelle_id'];
    $ad       = mysqli_real_escape_string($baglanti, $_POST['etkinlik_ad']);
    $konum    = mysqli_real_escape_string($baglanti, $_POST['konum']);
    $tarih    = mysqli_real_escape_string($baglanti, $_POST['tarih']);
    $tur      = mysqli_real_escape_string($baglanti, $_POST['tur']);
    $kontenjan = (int) $_POST['kontenjan'];

    mysqli_query($baglanti, "UPDATE etkinlikBilgi SET etkAd='$ad', etkKonum='$konum', etkTarih='$tarih', etkAlan='$tur', etkKontenjan='$kontenjan' WHERE etkinlikID='$gid'");
    $_SESSION['mesaj']     = "Etkinlik güncellendi.";
    $_SESSION['mesaj_tip'] = "success";
    header("Location: etkinlik_yonetici_panel.php");
    exit();
}

$mesaj = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ad       = mysqli_real_escape_string($baglanti, $_POST['etkinlik_ad']);
    $konum    = mysqli_real_escape_string($baglanti, $_POST['konum']);
    $tarih    = mysqli_real_escape_string($baglanti, $_POST['tarih']);
    $tur      = mysqli_real_escape_string($baglanti, $_POST['tur']);
    $kontenjan = (int) $_POST['kontenjan'];

    $sorgu = "INSERT INTO etkinlikBilgi (yoneticiID, etkAd, etkKonum, etkTarih, etkAlan, etkKontenjan)
          VALUES ('$yonetici_id', '$ad', '$konum', '$tarih', '$tur', '$kontenjan')";

    if (mysqli_query($baglanti, $sorgu)) {
        $_SESSION['mesaj']     = "Etkinlik başarıyla eklendi.";
        $_SESSION['mesaj_tip'] = "success";
    } else {
        $_SESSION['mesaj']     = "Hata: " . mysqli_error($baglanti);
        $_SESSION['mesaj_tip'] = "danger";
    }
    header("Location: etkinlik_yonetici_panel.php");
    exit();
}

if (isset($_SESSION['mesaj'])) {
    $tip   = htmlspecialchars($_SESSION['mesaj_tip']);
    $metin = htmlspecialchars($_SESSION['mesaj']);
    $mesaj = "<div class='alert alert-{$tip} alert-dismissible fade show' role='alert'>
                {$metin}
                <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
              </div>";
    unset($_SESSION['mesaj'], $_SESSION['mesaj_tip']);
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Volentra - Etkinlik Yönetimi</title>
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
          <li class="nav-item"><a class="nav-link" href="index.php">Anasayfa</a></li>
          <li class="nav-item"><a class="nav-link" href="yoneticiBagis.php">Bağışlar</a></li>
          <li class="nav-item"><a class="nav-link active" href="etkinlik_yonetici_panel.php">Etkinlikler</a></li>
          <li class="nav-item"><a class="nav-link" href="#profil">Profil Bilgileri</a></li>
          <li class="nav-item"><a class="nav-link" href="cikis.php">Çıkış Yap</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <main class="py-5 flex-grow-1">
    <div class="container">

      <!-- Yönetici Selamlama -->
      <div class="mb-4">
        <p class="text-secondary small mb-0">
          Hoş geldin, <strong>Yönetici</strong>
        </p>
        <p class="text-muted extra-small">
          E-posta: <?php echo htmlspecialchars($yonetici_mail); ?>
        </p>
      </div>

      <?php echo $mesaj; ?>

      <!-- ETKİNLİK EKLEME FORMU -->
      <section class="card card-volentra border-0 rounded-4 mb-5 shadow-sm">
        <div class="card-body p-4">
          <h2 class="h5 fw-bold mb-4 text-ebony border-start border-4 border-success ps-3">Yeni Etkinlik Ekle</h2>
          <form method="POST" action="etkinlik_yonetici_panel.php" class="row g-3">
            <div class="col-md-6">
              <label class="form-label fw-semibold">Etkinlik Adı</label>
              <input type="text" name="etkinlik_ad" class="form-control form-control-volentra" placeholder="Etkinlik başlığını girin" required>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">Konum</label>
              <input type="text" name="konum" class="form-control form-control-volentra" placeholder="Şehir veya Mekan" required>
            </div>
            <div class="col-md-3">
              <label class="form-label fw-semibold">Tarih</label>
              <input type="date" name="tarih" class="form-control form-control-volentra" required>
            </div>
            <div class="col-md-3">
              <label class="form-label fw-semibold">Tür</label>
              <select name="tur" class="form-select form-control-volentra" required>
                <option value="">Seçiniz...</option>
                <option value="Sağlık">Sağlık</option>
                <option value="Eğitim">Eğitim</option>
                <option value="Gıda Yardımı">Gıda Yardımı</option>
                <option value="Çevre">Çevre</option>
              </select>
            </div>
            <div class="col-md-2">
              <label class="form-label fw-semibold">Kontenjan</label>
              <input type="number" name="kontenjan" class="form-control form-control-volentra" placeholder="0" required>
            </div>
            <div class="col-12 text-end mt-4">
              <button type="submit" class="btn btn-volentra px-5">Etkinliği Yayınla</button>
            </div>
          </form>
        </div>
      </section>
      <?php if ($guncelle_verisi): ?>
<section class="card card-volentra border-0 rounded-4 mb-5 shadow-sm border-warning">
    <div class="card-body p-4">
        <h2 class="h5 fw-bold mb-4 text-ebony border-start border-4 border-warning ps-3">Etkinliği Güncelle — #<?php echo $guncelle_verisi['etkinlikID']; ?></h2>
        <form method="POST" action="etkinlik_yonetici_panel.php" class="row g-3">
            <input type="hidden" name="guncelle_id" value="<?php echo $guncelle_verisi['etkinlikID']; ?>">
            <div class="col-md-6">
                <label class="form-label fw-semibold">Etkinlik Adı</label>
                <input type="text" name="etkinlik_ad" class="form-control form-control-volentra" value="<?php echo htmlspecialchars($guncelle_verisi['etkAd']); ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Konum</label>
                <input type="text" name="konum" class="form-control form-control-volentra" value="<?php echo htmlspecialchars($guncelle_verisi['etkKonum']); ?>" required>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Tarih</label>
                <input type="date" name="tarih" class="form-control form-control-volentra" value="<?php echo $guncelle_verisi['etkTarih']; ?>" required>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Tür</label>
                <select name="tur" class="form-select form-control-volentra" required>
                    <option value="Sağlık" <?php echo $guncelle_verisi['etkAlan']==='Sağlık' ? 'selected' : ''; ?>>Sağlık</option>
                    <option value="Eğitim" <?php echo $guncelle_verisi['etkAlan']==='Eğitim' ? 'selected' : ''; ?>>Eğitim</option>
                    <option value="Gıda Yardımı" <?php echo $guncelle_verisi['etkAlan']==='Gıda Yardımı' ? 'selected' : ''; ?>>Gıda Yardımı</option>
                    <option value="Çevre" <?php echo $guncelle_verisi['etkAlan']==='Çevre' ? 'selected' : ''; ?>>Çevre</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold">Kontenjan</label>
                <input type="number" name="kontenjan" class="form-control form-control-volentra" value="<?php echo $guncelle_verisi['etkKontenjan']; ?>" required>
            </div>
            <div class="col-12 text-end mt-4">
                <a href="etkinlik_yonetici_panel.php" class="btn btn-secondary px-4 me-2">İptal</a>
                <button type="submit" class="btn btn-volentra px-5">Güncelle</button>
            </div>
        </form>
    </div>
</section>
<?php endif; ?>

      <!-- MEVCUT ETKİNLİKLER -->
      <section class="card card-volentra border-0 rounded-4 mb-5 shadow-sm">
        <div class="card-body p-4">
          <h2 class="h5 fw-bold mb-4 text-ebony border-start border-4 border-primary ps-3">Mevcut Etkinlikler</h2>
          <div class="table-responsive">
            <table class="table table-hover align-middle">
              <thead class="bg-soft">
                <tr>
                  <th>Etkinlik ID</th>
                  <th>Etkinlik Adı</th>
                  <th>Konum</th>
                  <th>Tarih</th>
                  <th>Tür</th>
                  <th>Kontenjan</th>
                  <th>İşlemler</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $etkinlikler = mysqli_query($baglanti, "SELECT * FROM etkinlikBilgi ORDER BY etkTarih DESC");
                if ($etkinlikler && mysqli_num_rows($etkinlikler) > 0) {
                    while ($e = mysqli_fetch_assoc($etkinlikler)) {
                        echo "<tr>
    <td class='id-chip'>#{$e['etkinlikID']}</td>
    <td class='fw-bold'>" . htmlspecialchars($e['etkAd']) . "</td>
    <td>" . htmlspecialchars($e['etkKonum']) . "</td>
    <td>" . date('d.m.Y', strtotime($e['etkTarih'])) . "</td>
    <td><span class='badge-type'>" . htmlspecialchars($e['etkAlan']) . "</span></td>
    <td class='fw-semibold'>{$e['etkKontenjan']}</td>
    <td>
        <div class='d-flex gap-2'>
            <a href='?guncelle_id={$e['etkinlikID']}' class='btn-action approve' title='Güncelle'><i class='ti ti-pencil'></i></a>
            <a href='?sil_id={$e['etkinlikID']}' class='btn-action reject' title='Sil' onclick='return confirm(\"Bu etkinliği silmek istediğinize emin misiniz?\")'><i class='ti ti-trash'></i></a>
        </div>
    </td>
</tr>";
                    }
                } else {
                    echo "<tr><td colspan='6' class='text-center py-4 text-muted'>Henüz etkinlik bulunmuyor.</td></tr>";
                }
                ?>
              </tbody>
            </table>
          </div>
        </div>
      </section>

      <!-- GELEN BAŞVURULAR -->
      <section class="card card-volentra border-0 rounded-4 shadow-sm">
        <div class="card-body p-4">
          <h2 class="h5 fw-bold mb-4 text-ebony border-start border-4 border-warning ps-3">Gelen Başvurular</h2>
          <div class="table-responsive">
            <table class="table table-hover align-middle">
              <thead class="bg-soft">
                <tr>
                  <th>Başvuru ID</th>
                  <th>Etkinlik ID</th>
                  <th>Gönüllü ID</th>
                  <th>Başvuru Tarihi</th>
                  <th>Durum</th>
                  <th>Karar</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $basvurular = mysqli_query($baglanti, "SELECT * FROM etkinlikBasvuruBilgi ORDER BY basvuru_tarih DESC");
                if ($basvurular && mysqli_num_rows($basvurular) > 0) {
                    while ($b = mysqli_fetch_assoc($basvurular)) {
                        if ($b['basvuru_durum'] === 'Onaylandı') {
                            $badge = "bg-success-subtle text-success";
                        } elseif ($b['basvuru_durum'] === 'Reddedildi') {
                            $badge = "bg-danger-subtle text-danger";
                        } else {
                            $badge = "bg-warning-subtle text-warning";
                        }

                        echo "<tr>
                            <td class='id-chip'>#{$b['basvuru_ID']}</td>
                            <td class='id-chip'>#{$b['etkinlik_ID']}</td>
                            <td class='id-chip'>#{$b['gonullu_ID']}</td>
                            <td>" . date('d.m.Y', strtotime($b['basvuru_tarih'])) . "</td>
                            <td><span class='badge {$badge} px-3 py-2 rounded-3'>{$b['basvuru_durum']}</span></td>
                            <td>
                                <div class='d-flex gap-2'>
                                    <a href='?karar=onayla&basvuru_id={$b['basvuru_ID']}' class='btn-action approve' title='Onayla'><i class='ti ti-check'></i></a>
                                    <a href='?karar=reddet&basvuru_id={$b['basvuru_ID']}' class='btn-action reject' title='Reddet'><i class='ti ti-x'></i></a>
                                </div>
                            </td>
                        </tr>";
                    }
                } else {
                    echo "<tr><td colspan='6' class='text-center py-4 text-muted'>Henüz başvuru bulunmuyor.</td></tr>";
                }
                ?>
              </tbody>
            </table>
          </div>
        </div>
      </section>

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