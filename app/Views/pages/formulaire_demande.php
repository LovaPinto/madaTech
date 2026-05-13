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
  $userName = $userName ?? 'Employé';
  $displayRole = $displayRole ?? 'Employé';
  $userEmail = $userEmail ?? '';
  $initials = $initials ?? 'EM';
  $typesConge = $typesConge ?? [];
  $soldes = $soldes ?? [];
  $year = $year ?? (int) date('Y');
?>
    
<!-- ╔══════════════════════════════════════════════════════════════╗
     ║  PAGE 3 — FORMULAIRE DEMANDE  (employe/create.php)          ║
     ╚══════════════════════════════════════════════════════════════╝ -->
<section id="page-form-conge" style="margin-top:3rem">
<div class="app-wrap">

  <aside class="sidebar">
    <div class="sidebar-brand">
      <div class="sidebar-logo-icon"><i class="bi bi-briefcase"></i></div>
      <div class="sidebar-brand-name">TechMada RH<span>Espace employé</span></div>
    </div>
    <ul class="sidebar-nav" style="margin-top:1rem">
      <li><a href="<?= route_to('dashboard') ?>"><i class="bi bi-grid-1x2"></i> Tableau de bord</a></li>
      <li><a href="<?= route_to('formulaire.demande') ?>" class="active"><i class="bi bi-plus-circle"></i> Nouvelle demande</a></li>
      <li><a href="<?= route_to('liste.demande') ?>"><i class="bi bi-calendar3"></i> Mes demandes</a></li>
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
        <div class="topbar-title">Nouvelle demande de congé</div>
        <div class="topbar-breadcrumb">
          <a href="<?= route_to('dashboard') ?>">Accueil</a>
          <i class="bi bi-chevron-right" style="font-size:.6rem"></i> Nouvelle demande
        </div>
      </div>
      <div class="topbar-actions">
        <a href="<?= route_to('logout') ?>" class="icon-btn" title="Déconnexion"><i class="bi bi-box-arrow-right"></i></a>
      </div>
    </div>

    <div class="content">
      <?php if (session()->getFlashdata('error')): ?>
        <div class="flash flash-error">
          <i class="bi bi-exclamation-circle-fill"></i>
          <?= esc(session()->getFlashdata('error')) ?>
        </div>
      <?php endif; ?>
      <?php if (session()->getFlashdata('success')): ?>
        <div class="flash flash-success">
          <i class="bi bi-check-circle-fill"></i>
          <?= esc(session()->getFlashdata('success')) ?>
        </div>
      <?php endif; ?>

      <div style="display:grid;grid-template-columns:1fr 300px;gap:1.5rem;align-items:start" class="form-layout">

        <!-- Formulaire principal -->
        <div>
          <div class="form-section">
            <h3>Détails de la demande</h3>

            <form action="<?= route_to('formulaire.demande.submit') ?>" method="post">
              <?= csrf_field() ?>
              <div class="f-group" style="margin-bottom:1rem">
                <label class="f-label">Type de congé <span style="color:var(--danger)">*</span></label>
                <select class="f-select" name="type_conge_id" required>
                  <option value="">-- Choisir un type --</option>
                  <?php foreach ($typesConge as $type): ?>
                    <option value="<?= esc((string) $type['id']) ?>" <?= old('type_conge_id') == $type['id'] ? 'selected' : '' ?>>
                      <?= esc($type['libelle']) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="form-grid-2" style="margin-bottom:1rem">
                <div class="f-group">
                  <label class="f-label">Date de début <span style="color:var(--danger)">*</span></label>
                  <input type="date" class="f-input" name="date_debut" value="<?= esc(old('date_debut')) ?>" required />
                </div>
                <div class="f-group">
                  <label class="f-label">Date de fin <span style="color:var(--danger)">*</span></label>
                  <input type="date" class="f-input" name="date_fin" value="<?= esc(old('date_fin')) ?>" required />
                </div>
              </div>

            <!-- Calcul automatique côté PHP (affiché après soumission ou en JS)
            <div class="f-computed">
              <div class="f-computed-num">5</div>
              <div class="f-computed-label">jours calendaires calculés<br><span style="font-size:.7rem;opacity:.7">du lundi 23 au vendredi 27 juin 2025</span></div>
            </div> -->

              <div class="f-group" style="margin-bottom:1rem">
                <label class="f-label">Motif (optionnel)</label>
                <textarea class="f-textarea" name="motif" placeholder="Précisez le motif de votre demande si nécessaire..."><?= esc(old('motif')) ?></textarea>
                <div class="f-hint">Le motif est visible par le responsable RH.</div>
              </div>

              <div class="form-actions">
                <button class="btn-forest" type="submit"><i class="bi bi-send"></i> Soumettre la demande</button>
                <a href="<?= route_to('dashboard') ?>" class="btn-secondary"><i class="bi bi-x"></i> Annuler</a>
              </div>
            </form>
          </div>
        </div>

        <!-- Panneau latéral : solde & règles -->
        <div style="display:flex;flex-direction:column;gap:1rem">
          <div class="data-card" style="margin:0">
            <div class="data-card-head">
              <h3><i class="bi bi-piggy-bank" style="color:var(--forest);margin-right:5px"></i>Vos soldes actuels — <?= esc((string) $year) ?></h3>
            </div>
            <div style="padding:.75rem 1.1rem;display:flex;flex-direction:column;gap:.75rem">
              <?php if (empty($soldes)): ?>
                <div class="td-muted" style="font-size:.8rem">Aucun solde disponible.</div>
              <?php else: ?>
                <?php foreach ($soldes as $solde): ?>
                  <?php
                    $attribues = (int) $solde['jours_attribues'];
                    $restants = (int) $solde['jours_restants'];
                    $percent = $attribues > 0 ? (int) round(($restants / $attribues) * 100) : 0;
                    $fillClass = $percent < 30 ? 'warn' : '';
                  ?>
                  <div>
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:4px">
                      <span style="font-size:.8rem;color:var(--ink)"><?= esc($solde['type_conge'] ?? '') ?></span>
                      <span style="font-family:'DM Mono',monospace;font-size:.8rem;color:var(--forest);font-weight:500">
                        <?= esc((string) $restants) ?> j
                      </span>
                    </div>
                    <div class="solde-bar"><div class="solde-fill <?= esc($fillClass) ?>" style="width:<?= esc((string) $percent) ?>%"></div></div>
                  </div>
                <?php endforeach; ?>
              <?php endif; ?>
            </div>
          </div>
          <div class="flash flash-info" style="margin:0">
            <i class="bi bi-info-circle-fill"></i>
            <span style="font-size:.8rem">Le solde est déduit uniquement à l'approbation de votre responsable.</span>
          </div>
          <div style="background:var(--cream);border:1px solid var(--border);border-radius:8px;padding:.85rem 1rem">
            <div style="font-size:.78rem;font-weight:500;color:var(--ink);margin-bottom:.5rem"><i class="bi bi-clipboard-check" style="color:var(--forest);margin-right:5px"></i>Rappel des règles</div>
            <ul style="margin:0;padding-left:1rem;font-size:.75rem;color:var(--muted);line-height:1.7">
              <li>Préavis minimum : 48h avant la date de début</li>
              <li>Pas de chevauchement avec une demande en cours</li>
              <li>Solde insuffisant = demande refusée automatiquement</li>
            </ul>
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