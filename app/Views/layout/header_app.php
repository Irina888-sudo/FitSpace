<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="<?= base_url('style.css') ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&family=Syne:wght@700;800&display=swap" rel="stylesheet" />
</head>
<body>

        <!-- SIDEBAR -->
    <aside class="sidebar">
      <div class="sidebar-logo">Fit<span>Space</span></div>

      <div class="sidebar-section">Menu</div>
      <ul class="sidebar-nav">
        <li><a href="#page-dashboard-client" class="active"><i class="bi bi-grid-1x2-fill"></i> Tableau de bord</a></li>
        <li><a href="#page-creneaux"><i class="bi bi-calendar3"></i> Voir les créneaux</a></li>
        <li>
          <a href="#page-mes-reservations">
            <i class="bi bi-bookmark-check-fill"></i> Mes réservations
            <span class="sidebar-badge urgent">2</span>
          </a>
        </li>
        <li><a href="#page-profil"><i class="bi bi-person-fill"></i> Mon profil</a></li>
      </ul>

      <div class="sidebar-footer">
        <div class="sidebar-user">
          <div class="avatar">JD</div>
          <div class="user-info">
            <div class="name">Jean Dupont</div>
            <div class="role">Client</div>
          </div>
          <a href="#page-login" style="margin-left:auto;color:rgba(255,255,255,0.3);font-size:1.1rem;" title="Déconnexion"><i class="bi bi-box-arrow-right"></i></a>
        </div>
      </div>
    </aside>

</body>
</html>