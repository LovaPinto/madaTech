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
  $departements = $departements ?? [];
  $statCounts = $statCounts ?? ['en_attente' => 0, 'approuvee' => 0, 'refusee' => 0, 'annulee' => 0];
  $statutFilter = $statutFilter ?? '';
  $departementFilter = $departementFilter ?? '';
  $statutLabels = [
    'en_attente' => ['label' => 'en attente', 'class' => 's-attente'],
    'approuvee' => ['label' => 'approuvée', 'class' => 's-approuvee'],
    'refusee' => ['label' => 'refusée', 'class' => 's-refusee'],
    'annulee' => ['label' => 'annulée', 'class' => 's-annulee'],
  ];
  $typeClasses = [
    'Congé annuel' => 't-annuel',
    'Congé maladie' => 't-maladie',
    'Congé spécial' => 't-special',
    'Congé sans solde' => 't-sans-solde',
  ];
?>
    
<!-- ╔══════════════════════════════════════════════════════════════╗
     ║  PAGE 5 — LISTE RH (VALIDATION)  (rh/index.php)             ║
     ╚══════════════════════════════════════════════════════════════╝ -->
<section id="page-liste-rh" style="margin-top:3rem">
<div class="app-wrap">

  <aside class="sidebar">
    <div class="sidebar-brand">
      <div class="sidebar-logo-icon"><i class="bi bi-person-check"></i></div>
      <div class="sidebar-brand-name">TechMada RH<span>Espace responsable</span></div>
    </div>
    <div class="sidebar-section">Menu</div>
    <ul class="sidebar-nav">
      <li><a href="<?= route_to('dashboard.rh') ?>"><i class="bi bi-grid-1x2"></i> Tableau de bord</a></li>
      <li>
        <a href="<?= route_to('dashboard.rh') ?>" class="active">
          <i class="bi bi-inbox"></i> Demandes à traiter
          <span class="nav-badge alert"><?= esc((string) $statCounts['en_attente']) ?></span>
        </a>
      </li>
      <li><a href="<?= route_to('dashboard.rh') ?>?statut=approuvee"><i class="bi bi-archive"></i> Historique</a></li>
      <li><a href="<?= route_to('dashboard.rh') ?>"><i class="bi bi-people"></i> Soldes employés</a></li>
    </ul>
    <div class="sidebar-user">
      <div class="s-user-row">
        <div class="avatar av-blue"><?= esc($initials ?? 'RH') ?></div>
        <div>
          <div class="user-name"><?= esc($userName ?? 'Responsable RH') ?></div>
          <div class="user-role"><?= esc($userEmail ?? '') ?></div>
        </div>
        <a href="<?= route_to('logout') ?>" style="margin-left:auto;color:rgba(255,255,255,.25);font-size:1.1rem"><i class="bi bi-box-arrow-right"></i></a>
      </div>
    </div>
  </aside>

  <div class="main">
    <div class="topbar">
      <div>
        <div class="topbar-title">Demandes à traiter</div>
        <div class="topbar-breadcrumb"><a href="<?= route_to('dashboard.rh') ?>">Accueil</a> <i class="bi bi-chevron-right" style="font-size:.6rem"></i> Demandes</div>
      </div>
      <div class="topbar-actions">
        <span style="font-size:.8rem;color:var(--muted);background:var(--warn-bg);border:1px solid var(--warn-br);border-radius:6px;padding:5px 10px;display:flex;align-items:center;gap:5px;color:var(--warn)">
          <i class="bi bi-hourglass-split"></i> <?= esc((string) $statCounts['en_attente']) ?> en attente
        </span>
        <a href="<?= route_to('logout') ?>" class="icon-btn" title="Déconnexion"><i class="bi bi-box-arrow-right"></i></a>
      </div>
    </div>

    <div class="content">

      <!-- Flash -->
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

      <!-- Filtre -->
      <form method="get" action="<?= route_to('dashboard.rh') ?>" style="display:flex;gap:8px;margin-bottom:1.25rem;flex-wrap:wrap">
        <a href="<?= route_to('dashboard.rh') ?>" style="padding:6px 14px;border-radius:20px;font-size:.8rem;font-weight:500;border:1.5px solid <?= $statutFilter === '' ? 'var(--forest)' : 'var(--border)' ?>;background:<?= $statutFilter === '' ? 'var(--forest)' : 'var(--white)' ?>;color:<?= $statutFilter === '' ? 'var(--white)' : 'var(--muted)' ?>;text-decoration:none">Tous (<?= esc((string) array_sum($statCounts)) ?>)</a>
        <a href="<?= route_to('dashboard.rh') ?>?statut=en_attente" style="padding:6px 14px;border-radius:20px;font-size:.8rem;font-weight:500;border:1.5px solid <?= $statutFilter === 'en_attente' ? 'var(--forest)' : 'var(--border)' ?>;background:<?= $statutFilter === 'en_attente' ? 'var(--forest)' : 'var(--white)' ?>;color:<?= $statutFilter === 'en_attente' ? 'var(--white)' : 'var(--muted)' ?>;text-decoration:none">En attente (<?= esc((string) $statCounts['en_attente']) ?>)</a>
        <a href="<?= route_to('dashboard.rh') ?>?statut=approuvee" style="padding:6px 14px;border-radius:20px;font-size:.8rem;font-weight:500;border:1.5px solid <?= $statutFilter === 'approuvee' ? 'var(--forest)' : 'var(--border)' ?>;background:<?= $statutFilter === 'approuvee' ? 'var(--forest)' : 'var(--white)' ?>;color:<?= $statutFilter === 'approuvee' ? 'var(--white)' : 'var(--muted)' ?>;text-decoration:none">Approuvées (<?= esc((string) $statCounts['approuvee']) ?>)</a>
        <a href="<?= route_to('dashboard.rh') ?>?statut=refusee" style="padding:6px 14px;border-radius:20px;font-size:.8rem;font-weight:500;border:1.5px solid <?= $statutFilter === 'refusee' ? 'var(--forest)' : 'var(--border)' ?>;background:<?= $statutFilter === 'refusee' ? 'var(--forest)' : 'var(--white)' ?>;color:<?= $statutFilter === 'refusee' ? 'var(--white)' : 'var(--muted)' ?>;text-decoration:none">Refusées (<?= esc((string) $statCounts['refusee']) ?>)</a>
        <select class="f-select" name="departement_id" style="font-size:.8rem;padding:6px 10px;width:auto;margin-left:auto" onchange="this.form.submit()">
          <option value="">Tous les départements</option>
          <?php foreach ($departements as $dep): ?>
            <option value="<?= esc((string) $dep['id']) ?>" <?= (string) $departementFilter === (string) $dep['id'] ? 'selected' : '' ?>>
              <?= esc($dep['nom']) ?>
            </option>
          <?php endforeach; ?>
        </select>
        <?php if ($statutFilter !== ''): ?>
          <input type="hidden" name="statut" value="<?= esc($statutFilter) ?>" />
        <?php endif; ?>
      </form>

      <div class="data-card">
        <div class="data-card-head"><h3>Toutes les demandes</h3></div>
        <table class="tbl">
          <thead>
            <tr><th>Employé</th><th>Type</th><th>Période</th><th>Durée</th><th>Solde dispo</th><th>Statut</th><th>Actions</th></tr>
          </thead>
          <tbody>
            <?php if (empty($demandes)): ?>
              <tr>
                <td colspan="7" class="td-muted">Aucune demande.</td>
              </tr>
            <?php else: ?>
              <?php foreach ($demandes as $demande): ?>
                <?php
                  $statut = $demande['statut'] ?? 'en_attente';
                  $statutInfo = $statutLabels[$statut] ?? ['label' => $statut, 'class' => 's-attente'];
                  $type = $demande['type'] ?? '';
                  $typeClass = $typeClasses[$type] ?? 't-annuel';
                  $du = $demande['date_debut'] ? date('d/m/Y', strtotime($demande['date_debut'])) : '-';
                  $au = $demande['date_fin'] ? date('d/m/Y', strtotime($demande['date_fin'])) : '-';
                  $joursRestants = $demande['jours_restants'] !== null ? (int) $demande['jours_restants'] : null;
                  $deductible = (int) ($demande['deductible'] ?? 1) === 1;
                  $insuffisant = $deductible && $joursRestants !== null && $joursRestants < (int) $demande['nb_jours'];
                ?>
                <tr>
                  <td>
                    <div class="profile-row">
                      <div class="avatar av-green" style="width:32px;height:32px;font-size:.7rem">
                        <?= esc(strtoupper(substr($demande['prenom'] ?? '', 0, 1) . substr($demande['nom'] ?? '', 0, 1))) ?>
                      </div>
                      <div class="profile-info">
                        <div class="pname"><?= esc(trim(($demande['prenom'] ?? '') . ' ' . ($demande['nom'] ?? ''))) ?></div>
                        <div class="pdept"><?= esc($demande['departement'] ?? '—') ?></div>
                      </div>
                    </div>
                  </td>
                  <td><span class="type-badge <?= esc($typeClass) ?>"><?= esc($type) ?></span></td>
                  <td class="td-muted" style="font-size:.8rem"><?= esc($du) ?> – <?= esc($au) ?></td>
                  <td class="td-mono"><?= esc((string) ($demande['nb_jours'] ?? 0)) ?> j</td>
                  <td>
                    <?php if ($joursRestants === null): ?>
                      <span style="font-family:'DM Mono',monospace;font-size:.82rem;color:var(--muted)">—</span>
                    <?php else: ?>
                      <span style="font-family:'DM Mono',monospace;font-size:.82rem;color:<?= $insuffisant ? 'var(--warn)' : 'var(--success)' ?>;font-weight:500">
                        <?= esc((string) $joursRestants) ?> j
                      </span>
                      <?php if ($insuffisant): ?>
                        <span style="font-size:.72rem;color:var(--danger)"> ⚠ insuffisant</span>
                      <?php else: ?>
                        <span style="font-size:.72rem;color:var(--muted)"> dispo</span>
                      <?php endif; ?>
                    <?php endif; ?>
                  </td>
                  <td><span class="statut <?= esc($statutInfo['class']) ?>"><?= esc($statutInfo['label']) ?></span></td>
                  <td>
                    <?php if ($statut === 'en_attente'): ?>
                      <form method="post" style="display:flex;gap:6px;flex-wrap:wrap">
                        <?= csrf_field() ?>
                        <input type="text" name="commentaire" class="f-input" style="min-width:160px;max-width:240px" placeholder="Commentaire (optionnel)" />
                        <button class="btn-sm btn-approve" formaction="<?= route_to('rh.demande.approve', $demande['id']) ?>" <?= $insuffisant ? 'disabled style="opacity:.4;cursor:not-allowed"' : '' ?>><i class="bi bi-check-lg"></i> Approuver</button>
                        <button class="btn-sm btn-refuse" formaction="<?= route_to('rh.demande.refuse', $demande['id']) ?>"><i class="bi bi-x-lg"></i> Refuser</button>
                      </form>
                    <?php else: ?>
                      <span class="td-muted" style="font-size:.75rem">—</span>
                    <?php endif; ?>
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