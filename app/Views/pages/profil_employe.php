<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= base_url('assets/style.css') ?>">
    <title>Profil</title>
</head>
<body>
<?php
  $userName = $userName ?? 'Employé';
  $displayRole = $displayRole ?? 'Employé';
  $userEmail = $userEmail ?? '';
  $initials = $initials ?? 'EM';
  $departement = $departement ?? '—';
  $dateEmbauche = $dateEmbauche ? date('d/m/Y', strtotime($dateEmbauche)) : '—';
?>

<section id="page-profil-employe" style="margin-top:3rem">
<div class="app-wrap">

  <aside class="sidebar">
    <div class="sidebar-brand">
      <div class="sidebar-logo-icon"><i class="bi bi-briefcase"></i></div>
      <div class="sidebar-brand-name">TechMada RH<span>Espace employé</span></div>
    </div>
    <ul class="sidebar-nav" style="margin-top:1rem">
      <li><a href="<?= route_to('dashboard') ?>"><i class="bi bi-grid-1x2"></i> Tableau de bord</a></li>
      <li><a href="<?= route_to('formulaire.demande') ?>"><i class="bi bi-plus-circle"></i> Nouvelle demande</a></li>
      <li><a href="<?= route_to('liste.demande') ?>"><i class="bi bi-calendar3"></i> Mes demandes</a></li>
      <li><a href="<?= route_to('profil.employe') ?>" class="active"><i class="bi bi-person"></i> Mon profil</a></li>
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
        <div class="topbar-title">Mon profil</div>
        <div class="topbar-breadcrumb"><a href="<?= route_to('dashboard') ?>">Accueil</a> <i class="bi bi-chevron-right" style="font-size:.6rem"></i> Profil</div>
      </div>
      <div class="topbar-actions">
        <a href="<?= route_to('logout') ?>" class="icon-btn" title="Déconnexion"><i class="bi bi-box-arrow-right"></i></a>
      </div>
    </div>

    <div class="content">
      <?php if (session()->getFlashdata('success')): ?>
        <div class="flash flash-success">
          <i class="bi bi-check-circle-fill"></i>
          <?= esc(session()->getFlashdata('success')) ?>
        </div>
      <?php endif; ?>
      <?php if (session()->getFlashdata('error')): ?>
        <div class="flash flash-error">
          <i class="bi bi-exclamation-circle-fill"></i>
          <?= esc(session()->getFlashdata('error')) ?>
        </div>
      <?php endif; ?>

      <div style="display:grid;grid-template-columns:1fr 360px;gap:1.5rem;align-items:start" class="form-layout">
        <div class="data-card">
          <div class="data-card-head"><h3>Informations personnelles</h3></div>
          <div style="padding:1rem 1.25rem;display:grid;gap:.75rem">
            <div style="display:flex;justify-content:space-between;gap:1rem">
              <span class="td-muted">Nom complet</span>
              <strong><?= esc($userName) ?></strong>
            </div>
            <div style="display:flex;justify-content:space-between;gap:1rem">
              <span class="td-muted">Email</span>
              <strong><?= esc($userEmail) ?></strong>
            </div>
            <div style="display:flex;justify-content:space-between;gap:1rem">
              <span class="td-muted">Rôle</span>
              <strong><?= esc($displayRole) ?></strong>
            </div>
            <div style="display:flex;justify-content:space-between;gap:1rem">
              <span class="td-muted">Département</span>
              <strong><?= esc($departement) ?></strong>
            </div>
            <div style="display:flex;justify-content:space-between;gap:1rem">
              <span class="td-muted">Date d'embauche</span>
              <strong><?= esc($dateEmbauche) ?></strong>
            </div>
          </div>
        </div>

        <div class="data-card">
          <div class="data-card-head"><h3>Modifier le mot de passe</h3></div>
          <div style="padding:1rem 1.25rem">
            <form action="<?= route_to('profil.password') ?>" method="post">
              <?= csrf_field() ?>
              <div class="f-group">
                <label class="f-label">Mot de passe actuel</label>
                <input type="password" class="f-input" name="current_password" required />
              </div>
              <div class="f-group">
                <label class="f-label">Nouveau mot de passe</label>
                <input type="password" class="f-input" name="new_password" required />
              </div>
              <div class="f-group">
                <label class="f-label">Confirmer</label>
                <input type="password" class="f-input" name="confirm_password" required />
              </div>
              <button class="btn-forest" type="submit"><i class="bi bi-check2"></i> Mettre à jour</button>
            </form>
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
