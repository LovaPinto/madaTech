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
  $stats = $stats ?? [
    'employes_actifs' => 0,
    'demandes_attente' => 0,
    'approuvees_mois' => 0,
    'departements' => 0,
    'absents_aujourdhui' => 0,
  ];
  $recentDemandes = $recentDemandes ?? [];
  $absents = $absents ?? [];
  $criticalSoldesCount = $criticalSoldesCount ?? 0;

  $userName = $userName ?? 'Administrateur';
  $userEmail = $userEmail ?? '';
  $initials = $initials ?? 'AD';

  $statutClasses = [
    'en_attente' => 's-attente',
    'approuvee' => 's-approuvee',
    'refusee' => 's-refusee',
    'annulee' => 's-annulee',
  ];

  $typeClass = function (?string $type): string {
    $type = strtolower($type ?? '');
    if (strpos($type, 'annuel') !== false) {
      return 't-annuel';
    }
    if (strpos($type, 'maladie') !== false) {
      return 't-maladie';
    }
    return 't-special';
  };

  $makeInitials = function (?string $name): string {
    $initials = '';
    foreach (preg_split('/\s+/', trim((string) $name)) as $part) {
      if ($part !== '') {
        $initials .= strtoupper(substr($part, 0, 1));
      }
    }
    return $initials !== '' ? $initials : 'AD';
  };
?>

<!-- ╔══════════════════════════════════════════════════════════════╗
     ║  PAGE 6 — DASHBOARD ADMIN  (admin/dashboard.php)            ║
     ╚══════════════════════════════════════════════════════════════╝ -->
<section id="page-dashboard-admin" style="margin-top:3rem">
<div class="app-wrap">

  <aside class="sidebar">
    <div class="sidebar-brand">
      <div class="sidebar-logo-icon" style="background:var(--ink);border:1px solid rgba(255,255,255,.15)"><i class="bi bi-shield-check" style="color:var(--leaf)"></i></div>
      <div class="sidebar-brand-name">TechMada RH
        <span>Administration</span>
      </div>
    </div>
    <div class="sidebar-section">Gestion</div>
    <ul class="sidebar-nav">
      <li><a href="<?= route_to('admin.dashboard') ?>" class="active"><i class="bi bi-speedometer2"></i> Vue d'ensemble</a></li>
      <li>
        <a href="<?= route_to('admin.historique') ?>">
          <i class="bi bi-inbox"></i> Toutes les demandes
          <?php if ((int) $stats['demandes_attente'] > 0): ?>
            <span class="nav-badge alert"><?= esc((string) $stats['demandes_attente']) ?></span>
          <?php endif; ?>
        </a>
      </li>
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
        <div class="topbar-title">Vue d'ensemble</div>
        <div class="topbar-breadcrumb">Administration</div>
      </div>
      <div class="topbar-actions">
        <a href="<?= route_to('admin.employes') ?>" class="btn-forest" style="padding:7px 14px;font-size:.82rem"><i class="bi bi-person-plus"></i> Ajouter un employé</a>
        <a href="<?= route_to('logout') ?>" class="icon-btn" title="Déconnexion"><i class="bi bi-box-arrow-right"></i></a>
      </div>
    </div>

    <div class="content">

      <!-- Métriques admin -->
      <div class="metrics">
        <div class="metric">
          <div class="metric-top"><div class="metric-icon mi-forest"><i class="bi bi-people"></i></div></div>
          <div class="metric-val"><?= esc((string) $stats['employes_actifs']) ?></div>
          <div class="metric-label">Employés actifs</div>
          <div class="metric-sub">Mise à jour aujourd'hui</div>
        </div>
        <div class="metric">
          <div class="metric-top"><div class="metric-icon mi-amber"><i class="bi bi-hourglass-split"></i></div></div>
          <div class="metric-val"><?= esc((string) $stats['demandes_attente']) ?></div>
          <div class="metric-label">Demandes en attente</div>
        </div>
        <div class="metric">
          <div class="metric-top"><div class="metric-icon mi-green"><i class="bi bi-calendar-check"></i></div></div>
          <div class="metric-val"><?= esc((string) $stats['approuvees_mois']) ?></div>
          <div class="metric-label">Approuvées ce mois</div>
          <div class="metric-sub">Total du mois en cours</div>
        </div>
        <div class="metric">
          <div class="metric-top"><div class="metric-icon mi-blue"><i class="bi bi-building"></i></div></div>
          <div class="metric-val"><?= esc((string) $stats['departements']) ?></div>
          <div class="metric-label">Départements</div>
        </div>
        <div class="metric">
          <div class="metric-top"><div class="metric-icon mi-red"><i class="bi bi-person-slash"></i></div></div>
          <div class="metric-val"><?= esc((string) $stats['absents_aujourdhui']) ?></div>
          <div class="metric-label">Absents aujourd'hui</div>
        </div>
      </div>

      <div style="display:grid;grid-template-columns:1fr 320px;gap:1.5rem;align-items:start">

        <!-- Demandes récentes -->
        <div class="data-card" style="margin:0">
          <div class="data-card-head">
            <h3>Demandes récentes</h3>
            <a href="<?= route_to('admin.historique') ?>" style="font-size:.8rem;color:var(--forest);text-decoration:none">Tout voir →</a>
          </div>
          <table class="tbl">
            <thead>
              <tr><th>Employé</th><th>Type</th><th>Durée</th><th>Statut</th></tr>
            </thead>
            <tbody>
              <?php if (empty($recentDemandes)): ?>
                <tr>
                  <td colspan="4" class="td-muted" style="text-align:center">Aucune demande récente.</td>
                </tr>
              <?php else: ?>
                <?php foreach ($recentDemandes as $demande): ?>
                  <?php
                    $fullName = trim(($demande['prenom'] ?? '') . ' ' . ($demande['nom'] ?? ''));
                    $initialsLocal = $makeInitials($fullName);
                    $typeLabel = $demande['type'] ?? '—';
                    $statut = $demande['statut'] ?? 'en_attente';
                    $statutClass = $statutClasses[$statut] ?? 's-attente';
                  ?>
                  <tr>
                    <td>
                      <div style="display:flex;align-items:center;gap:7px">
                        <div class="avatar av-green" style="width:28px;height:28px;font-size:.62rem"><?= esc($initialsLocal) ?></div>
                        <span class="td-name" style="font-size:.84rem"><?= esc($fullName) ?></span>
                      </div>
                    </td>
                    <td><span class="type-badge <?= esc($typeClass($typeLabel)) ?>"><?= esc($typeLabel) ?></span></td>
                    <td class="td-mono"><?= esc((string) ($demande['nb_jours'] ?? 0)) ?> j</td>
                    <td><span class="statut <?= esc($statutClass) ?>"><?= esc($statut) ?></span></td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>

        <!-- Absents du jour + soldes critiques -->
        <div style="display:flex;flex-direction:column;gap:1rem">
          <div class="data-card" style="margin:0">
            <div class="data-card-head"><h3><i class="bi bi-person-slash" style="color:var(--muted);margin-right:5px"></i>Absents aujourd'hui</h3></div>
            <div style="padding:.75rem 1.1rem;display:flex;flex-direction:column;gap:.6rem">
              <?php if (empty($absents)): ?>
                <div class="td-muted" style="font-size:.8rem">Aucun absent aujourd'hui.</div>
              <?php else: ?>
                <?php foreach ($absents as $absent): ?>
                  <?php
                    $absentName = trim(($absent['prenom'] ?? '') . ' ' . ($absent['nom'] ?? ''));
                    $absentInitials = $makeInitials($absentName);
                    $absentType = $absent['type'] ?? 'Congé';
                    $retour = $absent['date_fin'] ?? null;
                  ?>
                  <div style="display:flex;align-items:center;gap:8px">
                    <div class="avatar av-green" style="width:30px;height:30px;font-size:.65rem"><?= esc($absentInitials) ?></div>
                    <div>
                      <div style="font-size:.83rem;font-weight:500;color:var(--ink)"><?= esc($absentName) ?></div>
                      <div style="font-size:.72rem;color:var(--muted)"><?= esc($absentType) ?> · retour <?= esc((string) $retour) ?></div>
                    </div>
                  </div>
                <?php endforeach; ?>
              <?php endif; ?>
            </div>
          </div>
          <div class="flash flash-warn" style="margin:0">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <span style="font-size:.8rem">
              <?= esc((string) $criticalSoldesCount) ?> employé<?= $criticalSoldesCount > 1 ? 's' : '' ?> ont un solde critique (≤ 2 jours).
              <a href="<?= route_to('admin.soldes') ?>" style="color:var(--warn);font-weight:500">Voir les soldes →</a>
            </span>
          </div>
        </div>

      </div>

    </div>
    <div class="footer-app"><i class="bi bi-c-circle"></i> 2025 <span>TechMada RH</span></div>
  </div>

</div>
</section>


    
</body>
</html>