<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= base_url('assets/style.css') ?>">
    <title>Administration</title>
</head>
<body>
<?php
  $departements = $departements ?? [];
  $employes = $employes ?? [];
  $soldesAnnuel = $soldesAnnuel ?? [];
  $year = $year ?? (int) date('Y');
  $userName = $userName ?? 'Administrateur';
  $userEmail = $userEmail ?? '';
  $initials = $initials ?? 'AD';

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

<section id="page-admin-employes" style="margin-top:3rem">
<div class="app-wrap">

  <aside class="sidebar">
    <div class="sidebar-brand">
      <div class="sidebar-logo-icon" style="background:var(--ink);border:1px solid rgba(255,255,255,.15)"><i class="bi bi-shield-check" style="color:var(--leaf)"></i></div>
      <div class="sidebar-brand-name">TechMada RH<span>Administration</span></div>
    </div>
    <ul class="sidebar-nav" style="margin-top:1rem">
      <li><a href="<?= route_to('admin.dashboard') ?>"><i class="bi bi-speedometer2"></i> Vue d'ensemble</a></li>
      <li><a href="<?= route_to('admin.historique') ?>"><i class="bi bi-inbox"></i> Toutes les demandes</a></li>
      <li><a href="<?= route_to('admin.employes') ?>" class="active"><i class="bi bi-people"></i> Employés</a></li>
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
        <div class="topbar-title">Gestion des employés</div>
        <div class="topbar-breadcrumb"><a href="<?= route_to('admin.dashboard') ?>">Admin</a> <i class="bi bi-chevron-right" style="font-size:.6rem"></i> Employés</div>
      </div>
      <div class="topbar-actions">
        <a href="#form-ajout" class="btn-forest" style="padding:7px 14px;font-size:.82rem"><i class="bi bi-person-plus"></i> Ajouter</a>
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

      <!-- Formulaire ajout -->
      <form id="form-ajout" class="form-section" action="<?= route_to('admin.employes.store') ?>" method="post">
        <?= csrf_field() ?>
        <h3><i class="bi bi-person-plus" style="color:var(--forest);margin-right:6px"></i>Ajouter un employé</h3>
        <div class="form-grid-2" style="margin-bottom:1rem">
          <div class="f-group">
            <label class="f-label">Prénom</label>
            <input type="text" class="f-input" name="prenom" value="<?= esc(old('prenom') ?? '') ?>" placeholder="Jean"/>
          </div>
          <div class="f-group">
            <label class="f-label">Nom</label>
            <input type="text" class="f-input" name="nom" value="<?= esc(old('nom') ?? '') ?>" placeholder="Rakoto"/>
          </div>
          <div class="f-group">
            <label class="f-label">Email</label>
            <input type="email" class="f-input" name="email" value="<?= esc(old('email') ?? '') ?>" placeholder="jean.rakoto@techmada.mg"/>
          </div>
          <div class="f-group">
            <label class="f-label">Mot de passe initial</label>
            <input type="password" class="f-input" name="password" placeholder="À communiquer à l'employé"/>
          </div>
          <div class="f-group">
            <label class="f-label">Département</label>
            <select class="f-select" name="departement_id">
              <option value="">Aucun</option>
              <?php foreach ($departements as $dept): ?>
                <option value="<?= esc((string) $dept['id']) ?>" <?= old('departement_id') == $dept['id'] ? 'selected' : '' ?>><?= esc($dept['nom']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="f-group">
            <label class="f-label">Rôle</label>
            <select class="f-select" name="role">
              <option value="employe" <?= old('role') === 'employe' ? 'selected' : '' ?>>Employé</option>
              <option value="rh" <?= old('role') === 'rh' ? 'selected' : '' ?>>Responsable RH</option>
              <option value="admin" <?= old('role') === 'admin' ? 'selected' : '' ?>>Administrateur</option>
            </select>
          </div>
          <div class="f-group">
            <label class="f-label">Date d'embauche</label>
            <input type="date" class="f-input" name="date_embauche" value="<?= esc(old('date_embauche') ?? '') ?>"/>
          </div>
        </div>
        <div class="flash flash-info" style="margin-bottom:1rem">
          <i class="bi bi-info-circle-fill"></i>
          <span style="font-size:.82rem">Les soldes de congés seront initialisés automatiquement selon les types de congé configurés.</span>
        </div>
        <div class="form-actions">
          <button class="btn-forest" type="submit"><i class="bi bi-plus"></i> Créer l'employé</button>
          <a class="btn-secondary" href="<?= route_to('admin.employes') ?>">Réinitialiser</a>
        </div>
      </form>

      <!-- Liste employés -->
      <div class="data-card">
        <div class="data-card-head">
          <h3>Tous les employés</h3>
        </div>
        <table class="tbl">
          <thead>
            <tr><th>Employé</th><th>Département</th><th>Rôle</th><th>Embauche</th><th>Statut</th><th>Solde annuel</th><th>Actions</th></tr>
          </thead>
          <tbody>
            <?php if (empty($employes)): ?>
              <tr><td colspan="7" class="td-muted" style="text-align:center">Aucun employé trouvé.</td></tr>
            <?php else: ?>
              <?php foreach ($employes as $employe): ?>
                <?php
                  $fullName = trim(($employe['prenom'] ?? '') . ' ' . ($employe['nom'] ?? ''));
                  $empInitials = $makeInitials($fullName);
                  $solde = $soldesAnnuel[$employe['id']] ?? null;
                  $joursAttribues = $solde['jours_attribues'] ?? null;
                  $joursPris = $solde['jours_pris'] ?? null;
                  $joursRestants = is_numeric($joursAttribues) && is_numeric($joursPris)
                    ? (int) $joursAttribues - (int) $joursPris
                    : null;
                  $isActive = (int) ($employe['actif'] ?? 0) === 1;
                ?>
                <tr <?= $isActive ? '' : 'style="opacity:.5"' ?> >
                  <td>
                    <div class="profile-row">
                      <div class="avatar av-green" style="width:32px;height:32px;font-size:.68rem"><?= esc($empInitials) ?></div>
                      <div class="profile-info"><div class="pname"><?= esc($fullName) ?></div><div class="pdept"><?= esc($employe['email'] ?? '') ?></div></div>
                    </div>
                  </td>
                  <td class="td-muted"><?= esc($employe['departement'] ?? '—') ?></td>
                  <td><span class="type-badge" style="background:#f1efe8;color:#444441"><?= esc($employe['role'] ?? 'employe') ?></span></td>
                  <td class="td-muted td-mono" style="font-size:.78rem"><?= esc($employe['date_embauche'] ?? '—') ?></td>
                  <td><span class="statut <?= $isActive ? 's-approuvee' : 's-annulee' ?>" style="font-size:.68rem"><?= $isActive ? 'actif' : 'inactif' ?></span></td>
                  <td>
                    <?php if ($joursRestants !== null): ?>
                      <span style="font-family:'DM Mono',monospace;font-size:.82rem;color:var(--forest)"><?= esc((string) $joursRestants) ?> / <?= esc((string) $joursAttribues) ?> j</span>
                    <?php else: ?>
                      <span style="font-family:'DM Mono',monospace;font-size:.82rem;color:var(--muted)">— / — j</span>
                    <?php endif; ?>
                  </td>
                  <td>
                    <div class="action-btns">
                      <form action="<?= route_to('admin.employes.toggle', $employe['id']) ?>" method="post" style="display:inline">
                        <?= csrf_field() ?>
                        <button class="btn-sm <?= $isActive ? 'btn-del' : 'btn-view' ?>" type="submit">
                          <i class="bi <?= $isActive ? 'bi-slash-circle' : 'bi-arrow-counterclockwise' ?>"></i>
                          <?= $isActive ? 'Désactiver' : 'Réactiver' ?>
                        </button>
                      </form>
                    </div>
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