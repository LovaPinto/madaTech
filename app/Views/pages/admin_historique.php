<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= base_url('assets/style.css') ?>">
    <title>Historique des demandes</title>
</head>
<body>
<?php
  $demandes = $demandes ?? [];
  $userName = $userName ?? 'Administrateur';
  $userEmail = $userEmail ?? '';
  $initials = $initials ?? 'AD';

  $statutClasses = [
    'en_attente' => 's-attente',
    'approuvee' => 's-approuvee',
    'refusee' => 's-refusee',
    'annulee' => 's-annulee',
  ];
?>

<section id="page-admin-historique" style="margin-top:3rem">
<div class="app-wrap">

  <aside class="sidebar">
    <div class="sidebar-brand">
      <div class="sidebar-logo-icon" style="background:var(--ink);border:1px solid rgba(255,255,255,.15)"><i class="bi bi-shield-check" style="color:var(--leaf)"></i></div>
      <div class="sidebar-brand-name">TechMada RH<span>Administration</span></div>
    </div>
    <ul class="sidebar-nav" style="margin-top:1rem">
      <li><a href="<?= route_to('admin.dashboard') ?>"><i class="bi bi-speedometer2"></i> Vue d'ensemble</a></li>
      <li><a href="<?= route_to('admin.historique') ?>" class="active"><i class="bi bi-inbox"></i> Toutes les demandes</a></li>
      <li><a href="<?= route_to('admin.employes') ?>"><i class="bi bi-people"></i> Employés</a></li>
      <li><a href="<?= route_to('admin.departements') ?>"><i class="bi bi-building"></i> Départements</a></li>
      <li><a href="<?= route_to('admin.types') ?>"><i class="bi bi-tags"></i> Types de congé</a></li>
      <li><a href="<?= route_to('admin.soldes') ?>"><i class="bi bi-sliders"></i> Soldes annuels</a></li>
    </ul>
    <div class="sidebar-user">
      <div class="s-user-row">
        <div class="avatar" style="background:#5a2d82;width:32px;height:32px;font-size:.7rem"><?= esc($initials) ?></div>
        <div><div class="user-name"><?= esc($userName) ?></div><div class="user-role">Admin système</div></div>
        <a href="<?= route_to('logout') ?>" style="margin-left:auto;color:rgba(255,255,255,.25);font-size:1.1rem"><i class="bi bi-box-arrow-right"></i></a>
      </div>
    </div>
  </aside>

  <div class="main">
    <div class="topbar">
      <div>
        <div class="topbar-title">Historique des demandes</div>
        <div class="topbar-breadcrumb"><a href="<?= route_to('admin.dashboard') ?>">Admin</a> <i class="bi bi-chevron-right" style="font-size:.6rem"></i> Historique</div>
      </div>
      <div class="topbar-actions">
        <a href="<?= route_to('logout') ?>" class="icon-btn" title="Déconnexion"><i class="bi bi-box-arrow-right"></i></a>
      </div>
    </div>

    <div class="content">
      <div class="data-card">
        <div class="data-card-head">
          <h3>Toutes les demandes</h3>
        </div>
        <table class="tbl">
          <thead>
            <tr><th>Employé</th><th>Type</th><th>Période</th><th>Durée</th><th>Statut</th></tr>
          </thead>
          <tbody>
            <?php if (empty($demandes)): ?>
              <tr><td colspan="5" class="td-muted" style="text-align:center">Aucune demande trouvée.</td></tr>
            <?php else: ?>
              <?php foreach ($demandes as $demande): ?>
                <?php
                  $statut = $demande['statut'] ?? 'en_attente';
                  $statutClass = $statutClasses[$statut] ?? 's-attente';
                ?>
                <tr>
                  <td><?= esc(trim(($demande['prenom'] ?? '') . ' ' . ($demande['nom'] ?? ''))) ?></td>
                  <td class="td-muted"><?= esc($demande['type'] ?? '') ?></td>
                  <td class="td-mono"><?= esc($demande['date_debut'] ?? '') ?> → <?= esc($demande['date_fin'] ?? '') ?></td>
                  <td><?= esc((string) ($demande['nb_jours'] ?? 0)) ?> j</td>
                  <td><span class="statut <?= esc($statutClass) ?>"><?= esc($statut) ?></span></td>
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
