<?php 
session_start();
?>
<!DOCTYPE html>
<html lang="tr" class="scroll-behavior-smooth" style="scroll-behavior: smooth;">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Volentra - Anasayfa</title>
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
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#volentraNavbar" aria-controls="volentraNavbar" aria-expanded="false" aria-label="Menü">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="volentraNavbar">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-lg-center gap-lg-2">
                    <li class="nav-item"><a class="nav-link active" aria-current="page" href="#">Anasayfa</a></li>
                    <li class="nav-item"><a class="nav-link" href="gonulluBagis.php">Bağış Yap</a></li>
                    <li class="nav-item"><a class="nav-link" href="etkinlik_gonullu_panel.php">Etkinlikler</a></li>
                    <li class="nav-item"><?php if (isset($_SESSION['yetki'])): ?>
                    <a class="nav-link" href="cikis.php">Çıkış Yap</a>
                    <?php else: ?>
                    <a class="nav-link" href="giris.php">Giriş Yap</a>
                    <?php endif; ?>
                    </li>
                    <li class="nav-item"><a class="nav-link" href="profil.php">Profil Bilgileri</a></li>
                    <li class="nav-item"><a class="nav-link" href="#iletisim">İletişim</a></li>
                </ul>
            </div>
        </div>
    </nav>
    <main class="flex-grow-1">
        <section class="py-5 text-center text-white position-relative overflow-hidden bg-soft d-flex align-items-center" style="min-height: 450px; background: linear-gradient(135deg, rgba(65, 72, 51, 0.95) 0%, rgba(127, 85, 57, 0.75) 100%);">
            <div class="container my-5">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <span class="badge status-approved px-3 py-2 text-white border-white mb-3" style="background: rgba(255, 255, 255, 0.15);">Gönüllülük & Sosyal Yardımlaşma</span>
                        <h1 class="display-4 fw-black mb-3 text-white">İyilik İçin Bir Araya Gel</h1>
                        <p class="lead text-white-50 mb-4">Toplumsal fayda sağlamak, ihtiyaç sahiplerine el uzatmak ve dünyayı daha yaşanabilir kılmak için aktif projelerimize katılın.</p>
                        <div class="d-flex gap-3 justify-content-center">
                            <a href="#etkinlikler" class="btn btn-volentra px-4 py-3 btn-lg rounded-3 text-decoration-none">Etkinlikleri Keşfet</a>
                            <a href="#bagis" class="btn btn-outline-light px-4 py-3 btn-lg rounded-3 text-decoration-none fw-bold">Bağış Türleri</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section class="py-5 bg-soft" id="hakkimizda">
            <div class="container">
                <div class="row align-items-center g-5">
                    <div class="col-lg-6">
                        <span class="volentra-badge-pill mb-3">
                            <i class="ti ti-heart"></i> Biz Kimiz?
                        </span>
                        <h2 class="h3 fw-bold text-ebony mb-4">Dayanışmanın Gücüne İnanıyoruz</h2>
                        <p class="text-muted leading-relaxed mb-3">
                            <strong>Volentra</strong>, toplumsal yardımlaşmayı ve gönüllülük bilincini artırmak amacıyla kurulmuş yenilikçi bir sosyal sorumluluk platformudur. Eğitimden sağlığa, çevreden gıda yardımına kadar pek çok alanda gönüllülerimizle ihtiyaç sahiplerini güvenli bir köprüyle bir araya getiriyoruz.
                        </p>
                        <p class="text-muted leading-relaxed">
                            Her bir küçük dokunuşun büyük değişimler yaratabileceğini biliyor, projelerimizi şeffaflık, güven ve samimiyet esasıyla koordine ediyoruz. Siz de aramıza katılarak bu güzel değişimin bir parçası olabilirsiniz.
                        </p>
                    </div>
                    <div class="col-lg-6">
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <div class="card card-volentra border-0 rounded-4 p-4 text-center">
                                    <div class="text-success mb-3"><i class="ti ti-users fs-1"></i></div>
                                    <h4 class="h6 fw-bold text-ebony">Aktif Gönüllüler</h4>
                                    <p class="text-muted fs-7 mb-0">Yüzlerce aktif üye ile sahada ve dijitalde güç birliği.</p>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="card card-volentra border-0 rounded-4 p-4 text-center">
                                    <div class="text-success mb-3"><i class="ti ti-comet fs-1"></i></div>
                                    <h4 class="h6 fw-bold text-ebony">Hızlı Aksiyon</h4>
                                    <p class="text-muted fs-7 mb-0">Afet ve acil durumlarda hızlı koordinasyon imkanı.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section class="py-5" id="etkinlikler">
            <div class="container">
                <div class="text-center mb-5">
                    <span class="volentra-badge-pill mb-3">
                        <i class="ti ti-calendar-event"></i> Güncel Projeler
                    </span>
                    <h2 class="h3 fw-bold text-ebony">Gönüllülük Etkinlikleri</h2>
                    <p class="text-muted fs-7">Sizin için en uygun etkinliği seçin, hemen başvurarak iyiliğe ortak olun.</p>
                </div>
                <div class="row g-4">
                    <div class="col-md-4 d-flex">
                        <div class="card card-volentra border-0 rounded-4 shadow-sm w-100 d-flex flex-column justify-content-between p-4">
                            <div>
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="badge status-approved">Sağlık</span>
                                    <small class="text-muted fw-semibold"><i class="ti ti-users me-1"></i>25/50 Dolu</small>
                                </div>
                                <h3 class="h5 fw-bold text-ebony mb-2">Kan Bağışı Kampanyası</h3>
                                <p class="text-muted fs-7 mb-3">
                                    Kızılay iş birliğiyle düzenlediğimiz bu etkinlikte kan bağışında bulunarak umut olun, hayat kurtarın.
                                </p>
                                <div class="border-top border-light-subtle pt-3 mb-4">
                                    <div class="d-flex align-items-center gap-2 mb-2 text-secondary fs-7">
                                        <i class="ti ti-calendar-event fs-5 text-success"></i> <span>2026-05-20</span>
                                    </div>
                                    <div class="d-flex align-items-center gap-2 text-secondary fs-7">
                                        <i class="ti ti-map-pin fs-5 text-success"></i> <span>Diyarbakır</span>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <a href="etkinlik_gonullu.html" class="btn btn-volentra w-100 py-2 text-decoration-none text-center">Başvur</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 d-flex">
                        <div class="card card-volentra border-0 rounded-4 shadow-sm w-100 d-flex flex-column justify-content-between p-4">
                            <div>
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="badge status-pending" style="color: var(--coffee-bean); background: rgba(127, 85, 57, 0.18);">Eğitim</span>
                                    <small class="text-muted fw-semibold"><i class="ti ti-users me-1"></i>12/30 Dolu</small>
                                </div>
                                <h3 class="h5 fw-bold text-ebony mb-2">Köy Okulu Kütüphane Kurulumu</h3>
                                <p class="text-muted fs-7 mb-3">
                                    İhtiyacı olan bir köy okulumuz için topladığımız kitapları tasnif edip raflara dizecek neşeli eller arıyoruz.
                                </p>
                                <div class="border-top border-light-subtle pt-3 mb-4">
                                    <div class="d-flex align-items-center gap-2 mb-2 text-secondary fs-7">
                                        <i class="ti ti-calendar-event fs-5 text-success"></i> <span>2026-05-25</span>
                                    </div>
                                    <div class="d-flex align-items-center gap-2 text-secondary fs-7">
                                        <i class="ti ti-map-pin fs-5 text-success"></i> <span>Elazığ</span>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <a href="etkinlik_gonullu.html" class="btn btn-volentra w-100 py-2 text-decoration-none text-center">Başvur</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 d-flex">
                        <div class="card card-volentra border-0 rounded-4 shadow-sm w-100 d-flex flex-column justify-content-between p-4">
                            <div>
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="badge status-completed">Çevre</span>
                                    <small class="text-muted fw-semibold"><i class="ti ti-users-minus me-1"></i>Kayıt Kapalı</small>
                                </div>
                                <h3 class="h5 fw-bold text-ebony mb-2">Park Temizliği Etkinliği</h3>
                                <p class="text-muted fs-7 mb-3">
                                    Geleceğe daha temiz bir doğa bırakabilmek adına tüm çevre dostu gönüllülerimizle şehir parkını temizliyoruz.
                                </p>
                                <div class="border-top border-light-subtle pt-3 mb-4">
                                    <div class="d-flex align-items-center gap-2 mb-2 text-secondary fs-7">
                                        <i class="ti ti-calendar-event fs-5 text-muted"></i> <span>2026-04-10</span>
                                    </div>
                                    <div class="d-flex align-items-center gap-2 text-secondary fs-7">
                                        <i class="ti ti-map-pin fs-5 text-muted"></i> <span>Malatya</span>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <button class="btn btn-secondary w-100 py-2" disabled>Süresi Geçti</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section class="py-5 bg-soft" id="bagis">
            <div class="container">
                <div class="text-center mb-5">
                    <span class="volentra-badge-pill mb-3">
                        <i class="ti ti-coin"></i> Maddi ve Ayni Destek </span>
                    <h2 class="h3 fw-bold text-ebony">Destek Ol, Hayatlara Dokun</h2>
                    <p class="text-muted fs-7">Projelerimizin sürdürülebilirliği için bütçenize ya da imkanlarınıza uygun bağış türünü seçebilirsiniz.</p>
                </div>
                <div class="row g-4">
                    <div class="col-md-4 d-flex">
                        <div class="card card-volentra border-0 rounded-4 shadow-sm w-100 d-flex flex-column justify-content-between p-4">
                            <div class="text-center mb-3">
                                <div class="p-3 d-inline-block rounded-circle bg-soft text-success mb-3">
                                    <i class="ti ti-book-2 fs-1"></i>
                                </div>
                                <h3 class="h5 fw-bold text-ebony mb-2">Eğitim Bağışı</h3>
                                <p class="text-muted fs-7 mb-4">
                                    Köy okullarımıza kitap, kırtasiye malzemesi ve teknolojik ekipman desteği sağlayarak çocukların eğitimine katkıda bulunun.
                                </p>
                            </div>
                            <div>
                                <a href="#bagis-yap" class="btn btn-volentra w-100 py-2 text-decoration-none text-center">Bağış Yap</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 d-flex">
                        <div class="card card-volentra border-0 rounded-4 shadow-sm w-100 d-flex flex-column justify-content-between p-4">
                            <div class="text-center mb-3">
                                <div class="p-3 d-inline-block rounded-circle bg-soft text-success mb-3">
                                    <i class="ti ti-soup fs-1"></i>
                                </div>
                                <h3 class="h5 fw-bold text-ebony mb-2">Gıda ve Temel İhtiyaç</h3>
                                <p class="text-muted fs-7 mb-4">
                                    İhtiyaç sahibi aileler için hazırladığımız erzak paketlerine ve hijyen setlerine destek olarak mutfaklarına can suyu olun.
                                </p>
                            </div>
                            <div>
                                <a href="#bagis-yap" class="btn btn-volentra w-100 py-2 text-decoration-none text-center">Bağış Yap</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 d-flex">
                        <div class="card card-volentra border-0 rounded-4 shadow-sm w-100 d-flex flex-column justify-content-between p-4">
                            <div class="text-center mb-3">
                                <div class="p-3 d-inline-block rounded-circle bg-soft text-success mb-3">
                                    <i class="ti ti-shield-heart fs-1"></i>
                                </div>
                                <h3 class="h5 fw-bold text-ebony mb-2">Konut Bağışı</h3>
                                <p class="text-muted fs-7 mb-4">
                                    Deprem bölgesinde hala konteyner kentte yaşayan ihtiyaç sahibi vatandaşlarımız için konut bağışında bulunun.
                            </div>
                            <div>
                                <a href="#bagis-yap" class="btn btn-volentra w-100 py-2 text-decoration-none text-center">Bağış Yap</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
    <footer class="bg-ebony text-white mt-auto py-4">
        <div class="container">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2">
                <div class="fw-semibold">Volentra &copy; 2026</div>
                <div class="text-white-50">Gönüllülük ve sosyal yardımlaşma için bir araya gel.</div>
            </div>
        </div>
    </footer>
</body>
</html>