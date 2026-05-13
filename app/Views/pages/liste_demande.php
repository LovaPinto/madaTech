<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= base_url('assets/style.css') ?>">
    <title>Document</title>
</head>
<body>
<?php
  $demandes = $demandes ?? [];
  $stats = $stats ?? ['en_attente' => 0, 'approuvee' => 0, 'refusee' => 0, 'annulee' => 0];
  $typeClasses = [
    'Congé annuel' => 't-annuel',
    'Congé maladie' => 't-maladie',
    'Congé spécial' => 't-special',
    'Congé sans solde' => 't-sans-solde',
  ];
  $statutLabels = [
    'en_attente' => ['label' => 'en attente', 'class' => 's-attente'],
    'approuvee' => ['label' => 'approuvée', 'class' => 's-approuvee'],
    'refusee' => ['label' => 'refusée', 'class' => 's-refusee'],
    'annulee' => ['label' => 'annulée', 'class' => 's-annulee'],
  ];
?>
    
<!-- ╔══════════════════════════════════════════════════════════════╗
     ║  PAGE 4 — MES DEMANDES EMPLOYÉ  (employe/index.php)         ║
     ╚══════════════════════════════════════════════════════════════╝ -->
<section id="page-mes-conges" style="margin-top:3rem">
<div class="app-wrap">

  <aside class="sidebar">
    <div class="sidebar-brand">
      <div class="sidebar-logo-icon"><i class="bi bi-briefcase"></i></div>
      <div class="sidebar-brand-name">TechMada RH<span>Espace employé</span></div>
    </div>
    <ul class="sidebar-nav" style="margin-top:1rem">
      <li><a href="<?= route_to('dashboard') ?>"><i class="bi bi-grid-1x2"></i> Tableau de bord</a></li>
      <li><a href="<?= route_to('formulaire.demande') ?>"><i class="bi bi-plus-circle"></i> Nouvelle demande</a></li>
      <li><a href="<?= route_to('liste.demande') ?>" class="active"><i class="bi bi-calendar3"></i> Mes demandes
        <span class="nav-badge alert" style="margin-left:8px"><?= esc((string) ($stats['en_attente'] ?? 0)) ?></span>
      </a></li>
      <li><a href="<?= route_to('profil.employe') ?>"><i class="bi bi-person"></i> Mon profil</a></li>
    </ul>
    <div class="sidebar-user">
      <div class="s-user-row">
        <div class="avatar av-green"><?= esc($initials ?? 'EM') ?></div>
        <div>
          <div class="user-name"><?= esc($userName ?? 'Employé') ?></div>
          <div class="user-role">
            <?= esc($displayRole ?? 'Employé') ?><?= !empty($userEmail) ? ' · ' . esc($userEmail) : '' ?>
          </div>
        </div>
        <a href="<?= route_to('logout') ?>" style="margin-left:auto;color:rgba(255,255,255,.25);font-size:1.1rem" title="Déconnexion"><i class="bi bi-box-arrow-right"></i></a>
      </div>
    </div>
  </aside>

  <div class="main">
    <div class="topbar">
      <div>
        <div class="topbar-title">Mes demandes de congé</div>
        <div class="topbar-breadcrumb"><a href="<?= route_to('dashboard') ?>">Accueil</a> <i class="bi bi-chevron-right" style="font-size:.6rem"></i> Mes demandes</div>
      </div>
      <div class="topbar-actions">
        <a href="<?= route_to('formulaire.demande') ?>" class="btn-forest" style="padding:7px 14px;font-size:.82rem"><i class="bi bi-plus-lg"></i> Nouvelle demande</a>
        <a href="<?= route_to('logout') ?>" class="icon-btn" title="Déconnexion"><i class="bi bi-box-arrow-right"></i></a>
      </div>
    </div>

    <div class="content">
      <div class="data-card">
        <div class="data-card-head">
          <h3>Toutes mes demandes</h3>
          <div style="display:flex;gap:6px">
            <select class="f-select" style="font-size:.8rem;padding:6px 10px;width:auto">
              <option>Tous les statuts</option>
              <option>En attente</option>
              <option>Approuvée</option>
              <option>Refusée</option>
              <option>Annulée</option>
            </select>
          </div>
        </div>
        <table class="tbl">
          <thead>
            <tr><th>Type</th><th>Début</th><th>Fin</th><th>Durée</th><th>Statut</th><th>Commentaire RH</th><th>Action</th></tr>
          </thead>
          <tbody>
            <?php if (empty($demandes)): ?>
              <tr>
                <td colspan="7" class="td-muted">Aucune demande trouvée.</td>
              </tr>
            <?php else: ?>
              <?php foreach ($demandes as $demande): ?>
                <?php
                  $type = $demande['type'] ?? '';
                  $typeClass = $typeClasses[$type] ?? 't-annuel';
                  $statut = $demande['statut'] ?? 'en_attente';
                  $statutInfo = $statutLabels[$statut] ?? ['label' => $statut, 'class' => 's-attente'];
                  $debut = $demande['date_debut'] ? date('d/m/Y', strtotime($demande['date_debut'])) : '-';
                  $fin = $demande['date_fin'] ? date('d/m/Y', strtotime($demande['date_fin'])) : '-';
                  $duree = $demande['nb_jours'] ?? 0;
                  $commentaire = $demande['commentaire_rh'] ?? '';
                ?>
                <tr>
                  <td><span class="type-badge <?= esc($typeClass) ?>"><?= esc($type) ?></span></td>
                  <td class="td-muted"><?= esc($debut) ?></td>
                  <td class="td-muted"><?= esc($fin) ?></td>
                  <td class="td-mono"><?= esc((string) $duree) ?> j</td>
                  <td><span class="statut <?= esc($statutInfo['class']) ?>"><?= esc($statutInfo['label']) ?></span></td>
                  <td class="td-muted" style="font-size:.78rem">
                    <?= $commentaire !== '' ? esc($commentaire) : '—' ?>
                  </td>
                  <td><span class="td-muted" style="font-size:.75rem">—</span></td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
    <div class="footer-app"><i class="bi bi-c-circle"></i> 2025 <span>TechMada RH</span></div>
  </div>

</div>
</section>

</body>
</html>