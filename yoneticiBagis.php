<?php
  session_start();
  include 'baglanti.php';

  $mesaj = '';

  // Basvuru karar islemi (POST)
  if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['randevuKarar']) && isset($_POST['randevuID'])) {
    $randevuID = (int) $_POST['randevuID'];
    $randevuKarar = $_POST['randevuKarar'];

    $bagisDurum = null;
    if ($randevuKarar === 'onayla') {
      $bagisDurum = 'Onaylandı';
    } elseif ($randevuKarar === 'reddet') {
      $bagisDurum = 'Reddedildi';
    }

    if ($bagisDurum !== null && $randevuID > 0) {
      // Prepared Statement kullanarak guncelle
      $stmt = mysqli_prepare($baglanti, "UPDATE bagisRandevuBilgileri SET bagisBasvuruDurum = ? WHERE randevuID = ? LIMIT 1");
      if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'si', $bagisDurum, $randevuID);
        if (mysqli_stmt_execute($stmt)) {
          if (mysqli_stmt_affected_rows($stmt) >= 0) {
            $mesaj = "<div class='alert alert-success fw-bold alert-dismissible fade show' role='alert'>".
                     ($bagisDurum === 'Onaylandı' ? 'Başvuru başarıyla onaylandı.' : 'Başvuru başarıyla reddedildi.') .
                     "<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
          } else {
            $mesaj = "<div class='alert alert-warning fw-bold alert-dismissible fade show' role='alert'>Randevu güncellenemedi.</div>";
          }
        } else {
          $mesaj = "<div class='alert alert-danger fw-bold alert-dismissible fade show' role='alert'>Güncelleme hatası.</div>";
        }
        mysqli_stmt_close($stmt);
      } else {
        $mesaj = "<div class='alert alert-danger fw-bold alert-dismissible fade show' role='alert'>Hazırlıklı sorgu hazırlanamadı.</div>";
      }
    } else {
      $mesaj = "<div class='alert alert-warning fw-bold alert-dismissible fade show' role='alert'>Geçersiz istek.</div>";
    }
  }

  $yonetici_mail = $_SESSION['yMail'] ?? '';

  $yonetici_sorgu = mysqli_query($baglanti, "SELECT yID FROM yoneticiBilgi WHERE yMail = '" . mysqli_real_escape_string($baglanti, $yonetici_mail) . "' LIMIT 1");
  $yonetici_verisi = mysqli_fetch_assoc($yonetici_sorgu);
  $yonetici_id = $yonetici_verisi['yID'] ?? 0;
?>

<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Volentra - Bağış Randevu Yönetimi</title>

  <link rel="icon" type="image/x-icon" href="img/volentra_favicon.png">

  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@3.19.0/tabler-icons.min.css" />

  <link rel="stylesheet" href="style.css" />

  <!-- Tabler Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@3.19.0/tabler-icons.min.css" />
</head>
<body class="d-flex flex-column min-vh-100">

  <!-- NAVBAR -->
  <nav class="navbar navbar-expand-lg bg-ebony sticky-top" data-bs-theme="dark">
    <div class="container">
      <a class="navbar-brand d-flex align-items-center gap-2" href="#">
        <span class="volentra-logo" aria-hidden="true">
          <img src="img/volentra_logo.png" alt="Volentra" class="volentra-logo-img" />
        </span>
        <span class="volentra-brand-text">Volentra</span>
      </a>

      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#volentraNavbar" aria-controls="volentraNavbar" aria-expanded="false" aria-label="Menü">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="volentraNavbar">
        <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-lg-center gap-lg-2">
          <li class="nav-item"><a class="nav-link active" aria-current="page" href="index.php">Anasayfa</a></li>
          <li class="nav-item"><a class="nav-link" href="yoneticiBagis.html">Bağış Yap</a></li>
          <li class="nav-item"><a class="nav-link" href="etkinlik_yonetici_panel.php">Etkinlikler</a></li>
          <li class="nav-item"><a class="nav-link" href="#profil.php">Profil Bilgileri</a></li>
          <li class="nav-item"><a class="nav-link" href="cikis.php">Çıkış Yap</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <main class="py-5 flex-grow-1">
    <div class="container">

      <?php echo $mesaj; ?>

      <!-- ANA TABLO: TÜM RANDEVU TALEPLERİ -->
      <section id="randevu-yonetimi" class="card card-volentra border-0 rounded-4 mb-5 shadow-sm">
        <div class="card-body p-4">
          <h2 class="h5 fw-bold mb-4 text-ebony border-start border-4 border-primary ps-3">Tüm Randevu Talepleri</h2>

          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead class="bg-soft">
                <tr>
                  <th>Randevu ID</th>
                  <th>Bağış ID</th>
                  <th>Gönüllü ID</th>
                  <th>Kurum</th>
                  <th>Randevu Tarihi</th>
                  <th>Randevu Saati</th>
                  <th>Randevu Durumu</th>
                </tr>
              </thead>
              <tbody>
                <?php
                  $sql = "SELECT
                          r.randevuID,
                          r.gonulluID,
                          r.bagisTarihi,
                          r.bagisSaat,
                          r.bagisBasvuruDurum,
                          GROUP_CONCAT(DISTINCT k.kurum SEPARATOR ', ') AS kurumlar,
                          GROUP_CONCAT(DISTINCT k.kurumID SEPARATOR ', ') AS kurumIDs
                          FROM bagisRandevuBilgileri r
                          JOIN bagisBilgi b ON r.randevuID = b.randevuID
                          JOIN bagisKurumBilgileri k ON b.kurumID = k.kurumID
                          WHERE r.bagisBasvuruDurum = 'Beklemede'
                          GROUP BY r.randevuID
                          ORDER BY r.bagisTarihi DESC, r.bagisSaat DESC";

                  $res = mysqli_query($baglanti, $sql);
                  if ($res && mysqli_num_rows($res) > 0) {
                    while ($row = mysqli_fetch_assoc($res)) {
                      $randevuID = (int)$row['randevuID'];
                      $kurumlar = $row['kurumlar'] ?? '';
                      $kurumIDs = $row['kurumIDs'] ?? '';

                      echo "<tr>";
                      echo "  <td class='id-chip'>" . htmlspecialchars((string)$row['randevuID'], ENT_QUOTES, 'UTF-8') . "</td>";
                      echo "  <td class='id-chip'>" . htmlspecialchars((string)$kurumIDs, ENT_QUOTES, 'UTF-8') . "</td>";
                      echo "  <td class='id-chip'>" . htmlspecialchars((string)$row['gonulluID'], ENT_QUOTES, 'UTF-8') . "</td>";
                      echo "  <td>" . htmlspecialchars((string)$kurumlar, ENT_QUOTES, 'UTF-8') . "</td>";
                      echo "  <td>" . htmlspecialchars((string)$row['bagisTarihi'], ENT_QUOTES, 'UTF-8') . "</td>";
                      echo "  <td>" . htmlspecialchars((string)$row['bagisSaat'], ENT_QUOTES, 'UTF-8') . "</td>";
                      echo "  <td>";
                      echo "    <div class='d-flex align-items-center justify-content-between gap-2'>";
                      echo "      <span class='badge status-pending'>Beklemede</span>";
                      echo "      <div class='d-flex gap-2'>";

                      echo "        <form method='POST' action='" . htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') . "'>";
                      echo "          <input type='hidden' name='randevuID' value='" . $randevuID . "'>";
                      echo "          <input type='hidden' name='randevuKarar' value='onayla'>";
                      echo "          <button type='submit' class='btn-action approve' title='Onayla'><i class='ti ti-check'></i></button>";
                      echo "        </form>";

                      echo "        <form method='POST' action='" . htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') . "'>";
                      echo "          <input type='hidden' name='randevuID' value='" . $randevuID . "'>";
                      echo "          <input type='hidden' name='randevuKarar' value='reddet'>";
                      echo "          <button type='submit' class='btn-action reject' title='Reddet'><i class='ti ti-x'></i></button>";
                      echo "        </form>";

                      echo "      </div>";
                      echo "    </div>";
                      echo "  </td>";
                      echo "</tr>";
                    }
                  } else {
                    echo "<tr><td colspan='7' class='text-center py-4 text-muted'>Beklemede randevu bulunmuyor.</td></tr>";
                  }
                ?>
              </tbody>
            </table>
          </div>
        </div>
      </section>

      <!-- ALT KISIM: ONAYLANANLAR / REDDEDİLENLER -->
      <section id="etkinlik-yonetimi" class="row g-4 align-items-stretch">
        <div class="col-12 col-lg-6">
          <div class="card card-volentra border-0 rounded-4 shadow-sm h-100">
            <div class="card-body p-4">
              <h2 class="h5 fw-bold mb-4 text-ebony border-start border-4 border-success ps-3">Onaylananlar</h2>

              <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                  <thead class="bg-soft">
                    <tr>
                      <th>Randevu ID</th>
                      <th>Bağış ID</th>
                      <th>Gönüllü ID</th>
                      <th>Kurum</th>
                      <th>Randevu Tarihi</th>
                      <th>Randevu Saati</th>
                      <th>Randevu Durumu</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                      $sqlOnay = "SELECT
                                        r.randevuID,
                                        r.gonulluID,
                                        r.bagisTarihi,
                                        r.bagisSaat,
                                        r.bagisBasvuruDurum,
                                        GROUP_CONCAT(DISTINCT k.kurum SEPARATOR ', ') AS kurumlar,
                                        GROUP_CONCAT(DISTINCT k.kurumID SEPARATOR ', ') AS kurumIDs
                                  FROM bagisRandevuBilgileri r
                                  JOIN bagisBilgi b ON r.randevuID = b.randevuID
                                  JOIN bagisKurumBilgileri k ON b.kurumID = k.kurumID
                                  WHERE r.bagisBasvuruDurum = 'Onaylandı'
                                  GROUP BY r.randevuID
                                  ORDER BY r.bagisTarihi DESC, r.bagisSaat DESC";

                      $resOnay = mysqli_query($baglanti, $sqlOnay);
                      if ($resOnay && mysqli_num_rows($resOnay) > 0) {
                        while ($row = mysqli_fetch_assoc($resOnay)) {
                          echo "<tr>";
                          echo "  <td class='id-chip'>" . htmlspecialchars((string)$row['randevuID'], ENT_QUOTES, 'UTF-8') . "</td>";
                          echo "  <td class='id-chip'>" . htmlspecialchars((string)$row['kurumIDs'], ENT_QUOTES, 'UTF-8') . "</td>";
                          echo "  <td class='id-chip'>" . htmlspecialchars((string)$row['gonulluID'], ENT_QUOTES, 'UTF-8') . "</td>";
                          echo "  <td>" . htmlspecialchars((string)$row['kurumlar'], ENT_QUOTES, 'UTF-8') . "</td>";
                          echo "  <td>" . htmlspecialchars((string)$row['bagisTarihi'], ENT_QUOTES, 'UTF-8') . "</td>";
                          echo "  <td>" . htmlspecialchars((string)$row['bagisSaat'], ENT_QUOTES, 'UTF-8') . "</td>";
                          echo "  <td><span class='badge status-approved'>Onaylandı</span></td>";
                          echo "</tr>";
                        }
                      } else {
                        echo "<tr><td colspan='7' class='text-center py-4 text-muted'>Henüz onaylanan randevu yok.</td></tr>";
                      }
                    ?>
                  </tbody>
                </table>
              </div>

            </div>
          </div>
        </div>

        <div class="col-12 col-lg-6">
          <div class="card card-volentra border-0 rounded-4 shadow-sm h-100">
            <div class="card-body p-4">
              <h2 class="h5 fw-bold mb-4 text-ebony border-start border-4 border-danger ps-3">Reddedilenler</h2>

              <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                  <thead class="bg-soft">
                    <tr>
                      <th>Randevu ID</th>
                      <th>Bağış ID</th>
                      <th>Gönüllü ID</th>
                      <th>Kurum</th>
                      <th>Randevu Tarihi</th>
                      <th>Randevu Saati</th>
                      <th>Randevu Durumu</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                      $sqlRed = "SELECT
                                      r.randevuID,
                                      r.gonulluID,
                                      r.bagisTarihi,
                                      r.bagisSaat,
                                      r.bagisBasvuruDurum,
                                      GROUP_CONCAT(DISTINCT k.kurum SEPARATOR ', ') AS kurumlar,
                                      GROUP_CONCAT(DISTINCT k.kurumID SEPARATOR ', ') AS kurumIDs
                              FROM bagisRandevuBilgileri r
                              JOIN bagisBilgi b ON r.randevuID = b.randevuID
                              JOIN bagisKurumBilgileri k ON b.kurumID = k.kurumID
                              WHERE r.bagisBasvuruDurum = 'Reddedildi'
                              GROUP BY r.randevuID
                              ORDER BY r.bagisTarihi DESC, r.bagisSaat DESC";

                      $resRed = mysqli_query($baglanti, $sqlRed);
                      if ($resRed && mysqli_num_rows($resRed) > 0) {
                        while ($row = mysqli_fetch_assoc($resRed)) {
                          echo "<tr>";
                          echo "  <td class='id-chip'>" . htmlspecialchars((string)$row['randevuID'], ENT_QUOTES, 'UTF-8') . "</td>";
                          echo "  <td class='id-chip'>" . htmlspecialchars((string)($row['kurumIDs'] ?? ''), ENT_QUOTES, 'UTF-8') . "</td>";
                          echo "  <td class='id-chip'>" . htmlspecialchars((string)$row['gonulluID'], ENT_QUOTES, 'UTF-8') . "</td>";
                          echo "  <td>" . htmlspecialchars($row['kurumlar'] ?? '', ENT_QUOTES, 'UTF-8') . "</td>";
                          echo "  <td>" . htmlspecialchars($row['bagisTarihi'], ENT_QUOTES, 'UTF-8') . "</td>";
                          echo "  <td>" . htmlspecialchars($row['bagisSaat'], ENT_QUOTES, 'UTF-8') . "</td>";
                          echo "  <td><span class='badge status-rejected'>Reddedildi</span></td>";
                          echo "</tr>";
                        }
                      } else {
                        echo "<tr><td colspan='7' class='text-center py-4 text-muted'>Henüz reddedilen randevu yok.</td></tr>";
                      }
                    ?>
                  </tbody>
                </table>
              </div>

            </div>
          </div>
        </div>
      </section>

    </div>
  </main>

  <!-- FOOTER -->
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