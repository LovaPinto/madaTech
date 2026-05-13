<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= base_url('assets/style.css') ?>">
    <title>Soldes annuels</title>
</head>
<body>
<?php
  $employes = $employes ?? [];
  $types = $types ?? [];
  $soldes = $soldes ?? [];
  $year = $year ?? (int) date('Y');
  $userName = $userName ?? 'Administrateur';
  $userEmail = $userEmail ?? '';
  $initials = $initials ?? 'AD';
?>

<section id="page-admin-soldes" style="margin-top:3rem">
<div class="app-wrap">

  <aside class="sidebar">
    <div class="sidebar-brand">
      <div class="sidebar-logo-icon" style="background:var(--ink);border:1px solid rgba(255,255,255,.15)"><i class="bi bi-shield-check" style="color:var(--leaf)"></i></div>
      <div class="sidebar-brand-name">TechMada RH<span>Administration</span></div>
    </div>
    <ul class="sidebar-nav" style="margin-top:1rem">
      <li><a href="<?= route_to('admin.dashboard') ?>"><i class="bi bi-speedometer2"></i> Vue d'ensemble</a></li>
      <li><a href="<?= route_to('admin.historique') ?>"><i class="bi bi-inbox"></i> Toutes les demandes</a></li>
      <li><a href="<?= route_to('admin.employes') ?>"><i class="bi bi-people"></i> Employés</a></li>
      <li><a href="<?= route_to('admin.departements') ?>"><i class="bi bi-building"></i> Départements</a></li>
      <li><a href="<?= route_to('admin.types') ?>"><i class="bi bi-tags"></i> Types de congé</a></li>
      <li><a href="<?= route_to('admin.soldes') ?>" class="active"><i class="bi bi-sliders"></i> Soldes annuels</a></li>
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
        <div class="topbar-title">Soldes annuels</div>
        <div class="topbar-breadcrumb"><a href="<?= route_to('admin.dashboard') ?>">Admin</a> <i class="bi bi-chevron-right" style="font-size:.6rem"></i> Soldes annuels</div>
      </div>
      <div class="topbar-actions">
        <a href="<?= route_to('logout') ?>" class="icon-btn" title="Déconnexion"><i class="bi bi-box-arrow-right"></i></a>
      </div>
    </div>

    <div class="content">
      <?php if (session()->getFlashdata('success')): ?>
        <div class="flash flash-success"><i class="bi bi-check-circle-fill"></i> <?= esc(session()->getFlashdata('success')) ?></div>
      <?php endif; ?>
      <?php if (session()->getFlashdata('error')): ?>
        <div class="flash flash-error"><i class="bi bi-exclamation-circle-fill"></i> <?= esc(session()->getFlashdata('error')) ?></div>
      <?php endif; ?>

      <form class="form-section" action="<?= route_to('admin.soldes.store') ?>" method="post">
        <?= csrf_field() ?>
        <h3><i class="bi bi-sliders" style="color:var(--forest);margin-right:6px"></i>Initialiser / ajuster un solde</h3>
        <div class="form-grid-2" style="margin-bottom:1rem">
          <div class="f-group">
            <label class="f-label">Employé</label>
            <select class="f-select" name="employe_id">
              <option value="">Sélectionner</option>
              <?php foreach ($employes as $employe): ?>
                <option value="<?= esc((string) $employe['id']) ?>" <?= old('employe_id') == $employe['id'] ? 'selected' : '' ?>><?= esc($employe['prenom'] . ' ' . $employe['nom']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="f-group">
            <label class="f-label">Type de congé</label>
            <select class="f-select" name="type_conge_id">
              <option value="">Sélectionner</option>
              <?php foreach ($types as $type): ?>
                <option value="<?= esc((string) $type['id']) ?>" <?= old('type_conge_id') == $type['id'] ? 'selected' : '' ?>><?= esc($type['libelle']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="f-group">
            <label class="f-label">Année</label>
            <input type="number" class="f-input" name="annee" value="<?= esc(old('annee') ?? (string) $year) ?>"/>
          </div>
          <div class="f-group">
            <label class="f-label">Jours attribués</label>
            <input type="number" class="f-input" name="jours_attribues" value="<?= esc(old('jours_attribues') ?? '') ?>"/>
          </div>
          <div class="f-group">
            <label class="f-label">Jours pris</label>
            <input type="number" class="f-input" name="jours_pris" value="<?= esc(old('jours_pris') ?? '') ?>"/>
          </div>
        </div>
        <div class="form-actions">
          <button class="btn-forest" type="submit"><i class="bi bi-check2-circle"></i> Enregistrer</button>
          <a class="btn-secondary" href="<?= route_to('admin.soldes') ?>">Réinitialiser</a>
        </div>
      </form>

      <div class="data-card">
        <div class="data-card-head">
          <h3>Soldes actuels</h3>
        </div>
        <table class="tbl">
          <thead>
            <tr><th>Employé</th><th>Type</th><th>Année</th><th>Attribués</th><th>Pris</th><th>Restants</th></tr>
          </thead>
          <tbody>
            <?php if (empty($soldes)): ?>
              <tr><td colspan="6" class="td-muted" style="text-align:center">Aucun solde enregistré.</td></tr>
            <?php else: ?>
              <?php foreach ($soldes as $solde): ?>
                <?php
                  $attribues = (int) ($solde['jours_attribues'] ?? 0);
                  $pris = (int) ($solde['jours_pris'] ?? 0);
                  $restants = $attribues - $pris;
                ?>
                <tr>
                  <td><?= esc(trim(($solde['prenom'] ?? '') . ' ' . ($solde['nom'] ?? ''))) ?></td>
                  <td class="td-muted"><?= esc($solde['libelle'] ?? '') ?></td>
                  <td class="td-mono"><?= esc((string) ($solde['annee'] ?? $year)) ?></td>
                  <td><?= esc((string) $attribues) ?></td>
                  <td><?= esc((string) $pris) ?></td>
                  <td><?= esc((string) $restants) ?></td>
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
