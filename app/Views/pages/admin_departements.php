<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= base_url('assets/style.css') ?>">
    <title>Départements</title>
</head>
<body>
<?php
  $departements = $departements ?? [];
  $userName = $userName ?? 'Administrateur';
  $userEmail = $userEmail ?? '';
  $initials = $initials ?? 'AD';
?>

<section id="page-admin-departements" style="margin-top:3rem">
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
      <li><a href="<?= route_to('admin.departements') ?>" class="active"><i class="bi bi-building"></i> Départements</a></li>
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
        <div class="topbar-title">Départements</div>
        <div class="topbar-breadcrumb"><a href="<?= route_to('admin.dashboard') ?>">Admin</a> <i class="bi bi-chevron-right" style="font-size:.6rem"></i> Départements</div>
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

      <form class="form-section" action="<?= route_to('admin.departements.store') ?>" method="post">
        <?= csrf_field() ?>
        <h3><i class="bi bi-building" style="color:var(--forest);margin-right:6px"></i>Ajouter un département</h3>
        <div class="form-grid-2" style="margin-bottom:1rem">
          <div class="f-group">
            <label class="f-label">Nom</label>
            <input type="text" class="f-input" name="nom" value="<?= esc(old('nom') ?? '') ?>" placeholder="IT"/>
          </div>
          <div class="f-group">
            <label class="f-label">Description</label>
            <input type="text" class="f-input" name="description" value="<?= esc(old('description') ?? '') ?>" placeholder="Description courte"/>
          </div>
        </div>
        <div class="form-actions">
          <button class="btn-forest" type="submit"><i class="bi bi-plus"></i> Ajouter</button>
          <a class="btn-secondary" href="<?= route_to('admin.departements') ?>">Réinitialiser</a>
        </div>
      </form>

      <div class="data-card">
        <div class="data-card-head">
          <h3>Liste des départements</h3>
        </div>
        <table class="tbl">
          <thead>
            <tr><th>Nom</th><th>Description</th><th>Actions</th></tr>
          </thead>
          <tbody>
            <?php if (empty($departements)): ?>
              <tr><td colspan="3" class="td-muted" style="text-align:center">Aucun département.</td></tr>
            <?php else: ?>
              <?php foreach ($departements as $dept): ?>
                <tr>
                  <td><?= esc($dept['nom'] ?? '') ?></td>
                  <td class="td-muted"><?= esc($dept['description'] ?? '—') ?></td>
                  <td>
                    <form action="<?= route_to('admin.departements.delete', $dept['id']) ?>" method="post" style="display:inline">
                      <?= csrf_field() ?>
                      <button class="btn-sm btn-del" type="submit"><i class="bi bi-trash"></i> Supprimer</button>
                    </form>
                  </td>
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
