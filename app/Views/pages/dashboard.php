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
  $year = $year ?? (int) date('Y');
  $stats = $stats ?? ['en_attente' => 0, 'approuvee' => 0, 'refusee' => 0, 'annulee' => 0];
  $soldes = $soldes ?? [];
  $recentDemandes = $recentDemandes ?? [];
  $totalRestants = $totalRestants ?? 0;
  $totalAttribues = $totalAttribues ?? 0;

  $statutLabels = [
    'en_attente' => ['label' => 'en attente', 'class' => 's-attente'],
    'approuvee' => ['label' => 'approuvée', 'class' => 's-approuvee'],
    'refusee' => ['label' => 'refusée', 'class' => 's-refusee'],
    'annulee' => ['label' => 'annulée', 'class' => 's-annulee'],
  ];
?>

<section id="page-dashboard-employe" style="margin-top:3rem">
<div class="app-wrap">

  <!-- SIDEBAR EMPLOYÉ -->
  <aside class="sidebar">
    <div class="sidebar-brand">
      <div class="sidebar-logo-icon"><i class="bi bi-briefcase"></i></div>
      <div class="sidebar-brand-name">TechMada RH<span>Espace employé</span></div>
    </div>
    <div class="sidebar-section">Menu</div>
    <ul class="sidebar-nav">
      <li><a href="<?= route_to('dashboard') ?>" class="active"><i class="bi bi-grid-1x2"></i> Tableau de bord</a></li>
      <li><a href="<?= route_to('formulaire.demande') ?>"><i class="bi bi-plus-circle"></i> Nouvelle demande</a></li>
      <li>
        <a href="<?= route_to('liste.demande') ?>">
          <i class="bi bi-calendar3"></i> Mes demandes
          <span class="nav-badge alert"><?= esc((string) ($stats['en_attente'] ?? 0)) ?></span>
        </a>
      </li>
      <li><a href="<?= route_to('profil.employe') ?>"><i class="bi bi-person"></i> Mon profil</a></li>
    </ul>
    <div class="sidebar-user">
      <div class="s-user-row">
        <div class="avatar av-green"><?= esc($initials) ?></div>
        <div>
          <div class="user-name"><?= esc($userName) ?></div>
          <div class="user-role">
            <?= esc($displayRole) ?><?= $userEmail ? ' · ' . esc($userEmail) : '' ?>
          </div>
        </div>
        <a href="<?= route_to('logout') ?>" style="margin-left:auto;color:rgba(255,255,255,.25);font-size:1.1rem" title="Déconnexion"><i class="bi bi-box-arrow-right"></i></a>
      </div>
    </div>
  </aside>

  <div class="main">
    <div class="topbar">
      <div>
        <div class="topbar-title">Tableau de bord</div>
        <div class="topbar-breadcrumb">Accueil</div>
      </div>
      <div class="topbar-actions">
        <a href="<?= route_to('formulaire.demande') ?>" class="btn-forest" style="padding:7px 14px;font-size:.82rem">
          <i class="bi bi-plus-lg"></i> Nouvelle demande
        </a>
        <a href="<?= route_to('logout') ?>" class="icon-btn" title="Déconnexion"><i class="bi bi-box-arrow-right"></i></a>
      </div>
    </div>

    <div class="content">

      <!-- Flash succès -->
      <div class="flash flash-success">
        <i class="bi bi-check-circle-fill"></i>
        Votre demande de congé a bien été soumise. Elle est en attente de validation.
      </div>

      <!-- Métriques -->
      <div class="metrics">
        <div class="metric">
          <div class="metric-top"><div class="metric-icon mi-amber"><i class="bi bi-hourglass-split"></i></div></div>
          <div class="metric-val"><?= esc((string) ($stats['en_attente'] ?? 0)) ?></div>
          <div class="metric-label">En attente</div>
        </div>
        <div class="metric">
          <div class="metric-top"><div class="metric-icon mi-green"><i class="bi bi-check-circle"></i></div></div>
          <div class="metric-val"><?= esc((string) ($stats['approuvee'] ?? 0)) ?></div>
          <div class="metric-label">Approuvées</div>
        </div>
        <div class="metric">
          <div class="metric-top"><div class="metric-icon mi-forest"><i class="bi bi-calendar-check"></i></div></div>
          <div class="metric-val"><?= esc((string) $totalRestants) ?></div>
          <div class="metric-label">Jours restants</div>
          <div class="metric-sub">sur <?= esc((string) $totalAttribues) ?> cette année</div>
        </div>
        <div class="metric">
          <div class="metric-top"><div class="metric-icon mi-red"><i class="bi bi-x-circle"></i></div></div>
          <div class="metric-val"><?= esc((string) ($stats['refusee'] ?? 0)) ?></div>
          <div class="metric-label">Refusée</div>
        </div>
      </div>

      <!-- Soldes de congés -->
      <div class="data-card">
        <div class="data-card-head"><h3>Mes soldes de congés — <?= esc((string) $year) ?></h3></div>
        <div style="padding:1rem 1.25rem;display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:1rem">
          <?php if (empty($soldes)): ?>
            <div class="solde-card" style="margin:0">
              <div class="solde-header">
                <span class="solde-type">Aucun solde</span>
                <span class="solde-nums"><strong>0</strong> / 0 j</span>
              </div>
              <div class="solde-bar"><div class="solde-fill" style="width:0%"></div></div>
              <div class="solde-label">Aucune donnée disponible</div>
            </div>
          <?php else: ?>
            <?php foreach ($soldes as $solde): ?>
              <?php
                $attribues = (int) $solde['jours_attribues'];
                $restants = (int) $solde['jours_restants'];
                $pris = (int) $solde['jours_pris'];
                $percent = $attribues > 0 ? (int) round(($restants / $attribues) * 100) : 0;
                $fillClass = $percent < 30 ? 'warn' : '';
              ?>
              <div class="solde-card" style="margin:0">
                <div class="solde-header">
                  <span class="solde-type"><?= esc($solde['type_conge'] ?? '') ?></span>
                  <span class="solde-nums"><strong><?= esc((string) $restants) ?></strong> / <?= esc((string) $attribues) ?> j</span>
                </div>
                <div class="solde-bar"><div class="solde-fill <?= esc($fillClass) ?>" style="width:<?= esc((string) $percent) ?>%"></div></div>
                <div class="solde-label"><?= esc((string) $restants) ?> jours restants · <?= esc((string) $pris) ?> pris</div>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>

      <!-- Dernières demandes -->
      <div class="data-card">
        <div class="data-card-head">
          <h3>Mes dernières demandes</h3>
          <a href="<?= route_to('liste.demande') ?>" style="font-size:.8rem;color:var(--forest);text-decoration:none">Voir tout →</a>
        </div>
        <table class="tbl">
          <thead>
            <tr><th>Type</th><th>Du</th><th>Au</th><th>Durée</th><th>Statut</th><th>Action</th></tr>
          </thead>
          <tbody>
            <?php if (empty($recentDemandes)): ?>
              <tr>
                <td colspan="6" class="td-muted">Aucune demande récente.</td>
              </tr>
            <?php else: ?>
              <?php foreach ($recentDemandes as $demande): ?>
                <?php
                  $statut = $demande['statut'] ?? 'en_attente';
                  $statutInfo = $statutLabels[$statut] ?? ['label' => $statut, 'class' => 's-attente'];
                  $du = $demande['date_debut'] ? date('d/m/Y', strtotime($demande['date_debut'])) : '-';
                  $au = $demande['date_fin'] ? date('d/m/Y', strtotime($demande['date_fin'])) : '-';
                  $duree = $demande['nb_jours'] ?? 0;
                ?>
                <tr>
                  <td><span class="type-badge t-annuel"><?= esc($demande['type'] ?? '') ?></span></td>
                  <td class="td-muted"><?= esc($du) ?></td>
                  <td class="td-muted"><?= esc($au) ?></td>
                  <td class="td-mono"><?= esc((string) $duree) ?> j</td>
                  <td><span class="statut <?= esc($statutInfo['class']) ?>"><?= esc($statutInfo['label']) ?></span></td>
                  <td><span class="td-muted" style="font-size:.75rem">—</span></td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

    </div>
    <div class="footer-app"><i class="bi bi-c-circle"></i> 2025 <span>TechMada RH</span> — Projet CodeIgniter 4</div>
  </div>

</div>
</section>


    
</body>
</html>