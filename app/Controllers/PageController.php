<?php

namespace App\Controllers;

class PageController extends BaseController
{
    public function dashboardEmploye()
    {
        $role = session('user_role');
        if (! session('user_id')) {
            return redirect()->to(route_to('login'));
        }
        if ($role === 'admin') {
            return redirect()->to(route_to('dashboard.admin'));
        }
        if ($role === 'rh') {
            return redirect()->to(route_to('dashboard.rh'));
        }
        $userId = (int) session('user_id');
        $db = \Config\Database::connect();
        $year = (int) date('Y');

        $employe = $db->table('employes')
            ->select('employes.*, departements.nom AS departement')
            ->join('departements', 'departements.id = employes.departement_id', 'left')
            ->where('employes.id', $userId)
            ->get()
            ->getRowArray();

        $userName = $employe
            ? trim($employe['prenom'] . ' ' . $employe['nom'])
            : (session('user_name') ?? 'Employé');
        $userRole = $employe['role'] ?? (session('user_role') ?? 'employe');
        $userEmail = $employe['email'] ?? (session('user_email') ?? '');
        $displayRole = $userRole === 'admin'
            ? 'Administrateur'
            : ($userRole === 'rh' ? 'Responsable RH' : 'Employé');

        $initials = '';
        foreach (preg_split('/\s+/', trim($userName)) as $part) {
            if ($part !== '') {
                $initials .= strtoupper(substr($part, 0, 1));
            }
        }
        $initials = $initials !== '' ? $initials : 'EM';

        $stats = [
            'en_attente' => 0,
            'approuvee' => 0,
            'refusee' => 0,
            'annulee' => 0,
        ];
        $statusRows = $db->table('conges')
            ->select('statut, COUNT(*) AS total')
            ->where('employe_id', $userId)
            ->groupBy('statut')
            ->get()
            ->getResultArray();
        foreach ($statusRows as $row) {
            $stats[$row['statut']] = (int) $row['total'];
        }

        $soldes = $db->table('v_soldes')
            ->where('employe_id', $userId)
            ->where('annee', $year)
            ->get()
            ->getResultArray();

        $totalRestants = 0;
        $totalAttribues = 0;
        $totalPris = 0;
        foreach ($soldes as $solde) {
            $totalRestants += (int) $solde['jours_restants'];
            $totalAttribues += (int) $solde['jours_attribues'];
            $totalPris += (int) $solde['jours_pris'];
        }

        $recentDemandes = $db->table('conges c')
            ->select('c.id, c.date_debut, c.date_fin, c.nb_jours, c.statut, tc.libelle AS type')
            ->join('types_conge tc', 'tc.id = c.type_conge_id', 'left')
            ->where('c.employe_id', $userId)
            ->orderBy('c.date_debut', 'DESC')
            ->limit(3)
            ->get()
            ->getResultArray();

        return view('pages/dashboard', [
            'userName' => $userName,
            'userRole' => $userRole,
            'userEmail' => $userEmail,
            'displayRole' => $displayRole,
            'initials' => $initials,
            'stats' => $stats,
            'soldes' => $soldes,
            'year' => $year,
            'totalRestants' => $totalRestants,
            'totalAttribues' => $totalAttribues,
            'totalPris' => $totalPris,
            'recentDemandes' => $recentDemandes,
            'departement' => $employe['departement'] ?? null,
        ]);
    }

    public function dashboardAdmin()
    {
        return view('pages/dasboard_admin');
    }

    public function dashboardRh()
    {
        $role = session('user_role');
        if (! session('user_id')) {
            return redirect()->to(route_to('login'));
        }
        if ($role !== 'rh') {
            return redirect()->to(route_to('dashboard'));
        }

        $db = \Config\Database::connect();
        $userId = (int) session('user_id');
        $year = (int) date('Y');

        $statutFilter = (string) $this->request->getGet('statut');
        $departementFilter = (string) $this->request->getGet('departement_id');

        $employe = $db->table('employes')
            ->select('employes.*, departements.nom AS departement')
            ->join('departements', 'departements.id = employes.departement_id', 'left')
            ->where('employes.id', $userId)
            ->get()
            ->getRowArray();

        $userName = $employe
            ? trim($employe['prenom'] . ' ' . $employe['nom'])
            : (session('user_name') ?? 'Responsable RH');
        $userEmail = $employe['email'] ?? (session('user_email') ?? '');

        $initials = '';
        foreach (preg_split('/\s+/', trim($userName)) as $part) {
            if ($part !== '') {
                $initials .= strtoupper(substr($part, 0, 1));
            }
        }
        $initials = $initials !== '' ? $initials : 'RH';

        $departements = $db->table('departements')
            ->select('id, nom')
            ->orderBy('nom', 'ASC')
            ->get()
            ->getResultArray();

        $baseQuery = $db->table('conges c')
            ->select('c.id, c.date_debut, c.date_fin, c.nb_jours, c.statut, c.commentaire_rh, c.motif, c.created_at, tc.libelle AS type, tc.deductible, e.prenom, e.nom, d.nom AS departement, d.id AS departement_id, (s.jours_attribues - s.jours_pris) AS jours_restants')
            ->join('employes e', 'e.id = c.employe_id')
            ->join('departements d', 'd.id = e.departement_id', 'left')
            ->join('types_conge tc', 'tc.id = c.type_conge_id', 'left')
            ->join('soldes s', 's.employe_id = e.id AND s.type_conge_id = tc.id AND s.annee = ' . $db->escape($year), 'left');

        if ($statutFilter !== '') {
            $baseQuery->where('c.statut', $statutFilter);
        }
        if ($departementFilter !== '') {
            $baseQuery->where('d.id', (int) $departementFilter);
        }

        $demandes = $baseQuery
            ->orderBy('c.created_at', 'DESC')
            ->get()
            ->getResultArray();

        $statCounts = [
            'en_attente' => 0,
            'approuvee' => 0,
            'refusee' => 0,
            'annulee' => 0,
        ];
        $countQuery = $db->table('conges c')
            ->select('c.statut, COUNT(*) AS total')
            ->join('employes e', 'e.id = c.employe_id')
            ->join('departements d', 'd.id = e.departement_id', 'left');
        if ($departementFilter !== '') {
            $countQuery->where('d.id', (int) $departementFilter);
        }
        $countRows = $countQuery->groupBy('c.statut')->get()->getResultArray();
        foreach ($countRows as $row) {
            $statCounts[$row['statut']] = (int) $row['total'];
        }

        return view('pages/liste_rh', [
            'userName' => $userName,
            'userEmail' => $userEmail,
            'initials' => $initials,
            'demandes' => $demandes,
            'departements' => $departements,
            'statCounts' => $statCounts,
            'statutFilter' => $statutFilter,
            'departementFilter' => $departementFilter,
            'year' => $year,
        ]);
    }

    public function approveDemandeRh($id)
    {
        $role = session('user_role');
        if (! session('user_id')) {
            return redirect()->to(route_to('login'));
        }
        if ($role !== 'rh') {
            return redirect()->to(route_to('dashboard'));
        }

        $demandeId = (int) $id;
        $commentaire = (string) $this->request->getPost('commentaire');

        $db = \Config\Database::connect();
        $demande = $db->table('conges c')
            ->select('c.id, c.employe_id, c.type_conge_id, c.date_debut, c.nb_jours, c.statut, tc.deductible')
            ->join('types_conge tc', 'tc.id = c.type_conge_id', 'left')
            ->where('c.id', $demandeId)
            ->get()
            ->getRowArray();

        if (! $demande || $demande['statut'] !== 'en_attente') {
            return redirect()->back()->with('error', 'Demande introuvable ou déjà traitée.');
        }

        $year = (int) date('Y', strtotime($demande['date_debut']));

        $db->transStart();

        $db->table('conges')
            ->where('id', $demandeId)
            ->update([
                'statut' => 'approuvee',
                'commentaire_rh' => $commentaire !== '' ? $commentaire : null,
                'traite_par' => (int) session('user_id'),
            ]);

        if ((int) ($demande['deductible'] ?? 1) === 1) {
            $solde = $db->table('soldes')
                ->select('id, jours_pris')
                ->where('employe_id', (int) $demande['employe_id'])
                ->where('type_conge_id', (int) $demande['type_conge_id'])
                ->where('annee', $year)
                ->get()
                ->getRowArray();

            if ($solde) {
                $db->table('soldes')
                    ->where('id', $solde['id'])
                    ->update([
                        'jours_pris' => (int) $solde['jours_pris'] + (int) $demande['nb_jours'],
                    ]);
            } else {
                $db->table('soldes')->insert([
                    'employe_id' => (int) $demande['employe_id'],
                    'type_conge_id' => (int) $demande['type_conge_id'],
                    'annee' => $year,
                    'jours_attribues' => 0,
                    'jours_pris' => (int) $demande['nb_jours'],
                ]);
            }
        }

        $db->transComplete();

        return redirect()->back()->with('success', 'Demande approuvée et solde mis à jour.');
    }

    public function refuseDemandeRh($id)
    {
        $role = session('user_role');
        if (! session('user_id')) {
            return redirect()->to(route_to('login'));
        }
        if ($role !== 'rh') {
            return redirect()->to(route_to('dashboard'));
        }

        $demandeId = (int) $id;
        $commentaire = (string) $this->request->getPost('commentaire');

        $db = \Config\Database::connect();
        $demande = $db->table('conges')
            ->select('id, statut')
            ->where('id', $demandeId)
            ->get()
            ->getRowArray();

        if (! $demande || $demande['statut'] !== 'en_attente') {
            return redirect()->back()->with('error', 'Demande introuvable ou déjà traitée.');
        }

        $db->table('conges')
            ->where('id', $demandeId)
            ->update([
                'statut' => 'refusee',
                'commentaire_rh' => $commentaire !== '' ? $commentaire : null,
                'traite_par' => (int) session('user_id'),
            ]);

        return redirect()->back()->with('success', 'Demande refusée.');
    }

    public function listeDemandeEmploye()
    {
        $role = session('user_role');
        if (! session('user_id')) {
            return redirect()->to(route_to('login'));
        }
        if ($role === 'admin') {
            return redirect()->to(route_to('dashboard.admin'));
        }
        if ($role === 'rh') {
            return redirect()->to(route_to('dashboard.rh'));
        }

        $userId = (int) session('user_id');
        $db = \Config\Database::connect();

        $employe = $db->table('employes')
            ->select('employes.*, departements.nom AS departement')
            ->join('departements', 'departements.id = employes.departement_id', 'left')
            ->where('employes.id', $userId)
            ->get()
            ->getRowArray();

        $userName = $employe
            ? trim($employe['prenom'] . ' ' . $employe['nom'])
            : (session('user_name') ?? 'Employé');
        $userRole = $employe['role'] ?? (session('user_role') ?? 'employe');
        $userEmail = $employe['email'] ?? (session('user_email') ?? '');
        $displayRole = $userRole === 'admin'
            ? 'Administrateur'
            : ($userRole === 'rh' ? 'Responsable RH' : 'Employé');

        $initials = '';
        foreach (preg_split('/\s+/', trim($userName)) as $part) {
            if ($part !== '') {
                $initials .= strtoupper(substr($part, 0, 1));
            }
        }
        $initials = $initials !== '' ? $initials : 'EM';

        $demandes = $db->table('conges c')
            ->select('c.id, c.date_debut, c.date_fin, c.nb_jours, c.statut, c.commentaire_rh, tc.libelle AS type')
            ->join('types_conge tc', 'tc.id = c.type_conge_id', 'left')
            ->where('c.employe_id', $userId)
            ->orderBy('c.date_debut', 'DESC')
            ->get()
            ->getResultArray();

        $stats = [
            'en_attente' => 0,
            'approuvee' => 0,
            'refusee' => 0,
            'annulee' => 0,
        ];
        $statusRows = $db->table('conges')
            ->select('statut, COUNT(*) AS total')
            ->where('employe_id', $userId)
            ->groupBy('statut')
            ->get()
            ->getResultArray();
        foreach ($statusRows as $row) {
            $stats[$row['statut']] = (int) $row['total'];
        }

        return view('pages/liste_demande', [
            'userName' => $userName,
            'userRole' => $userRole,
            'userEmail' => $userEmail,
            'displayRole' => $displayRole,
            'initials' => $initials,
            'departement' => $employe['departement'] ?? null,
            'demandes' => $demandes,
            'stats' => $stats,
        ]);
    }

    public function formulaireDemandeEmploye()
    {
        $role = session('user_role');
        if (! session('user_id')) {
            return redirect()->to(route_to('login'));
        }
        if ($role === 'admin') {
            return redirect()->to(route_to('dashboard.admin'));
        }
        if ($role === 'rh') {
            return redirect()->to(route_to('dashboard.rh'));
        }

        $userId = (int) session('user_id');
        $db = \Config\Database::connect();
        $year = (int) date('Y');

        $employe = $db->table('employes')
            ->select('employes.*, departements.nom AS departement')
            ->join('departements', 'departements.id = employes.departement_id', 'left')
            ->where('employes.id', $userId)
            ->get()
            ->getRowArray();

        $userName = $employe
            ? trim($employe['prenom'] . ' ' . $employe['nom'])
            : (session('user_name') ?? 'Employé');
        $userRole = $employe['role'] ?? (session('user_role') ?? 'employe');
        $userEmail = $employe['email'] ?? (session('user_email') ?? '');
        $displayRole = $userRole === 'admin'
            ? 'Administrateur'
            : ($userRole === 'rh' ? 'Responsable RH' : 'Employé');

        $initials = '';
        foreach (preg_split('/\s+/', trim($userName)) as $part) {
            if ($part !== '') {
                $initials .= strtoupper(substr($part, 0, 1));
            }
        }
        $initials = $initials !== '' ? $initials : 'EM';

        $typesConge = $db->table('types_conge')
            ->select('id, libelle')
            ->orderBy('libelle', 'ASC')
            ->get()
            ->getResultArray();

        $soldes = $db->table('v_soldes')
            ->where('employe_id', $userId)
            ->where('annee', $year)
            ->get()
            ->getResultArray();

        return view('pages/formulaire_demande', [
            'userName' => $userName,
            'userRole' => $userRole,
            'userEmail' => $userEmail,
            'displayRole' => $displayRole,
            'initials' => $initials,
            'departement' => $employe['departement'] ?? null,
            'typesConge' => $typesConge,
            'soldes' => $soldes,
            'year' => $year,
        ]);
    }

    public function submitDemandeEmploye()
    {
        $role = session('user_role');
        if (! session('user_id')) {
            return redirect()->to(route_to('login'));
        }
        if ($role === 'admin') {
            return redirect()->to(route_to('dashboard.admin'));
        }
        if ($role === 'rh') {
            return redirect()->to(route_to('dashboard.rh'));
        }

        $rules = [
            'type_conge_id' => 'required|is_natural_no_zero',
            'date_debut' => 'required|valid_date',
            'date_fin' => 'required|valid_date',
            'motif' => 'permit_empty|string',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Veuillez vérifier les champs du formulaire.');
        }

        $typeCongeId = (int) $this->request->getPost('type_conge_id');
        $dateDebut = (string) $this->request->getPost('date_debut');
        $dateFin = (string) $this->request->getPost('date_fin');
        $motif = (string) $this->request->getPost('motif');

        $start = new \DateTimeImmutable($dateDebut);
        $end = new \DateTimeImmutable($dateFin);
        if ($end < $start) {
            return redirect()->back()->withInput()->with('error', 'La date de fin doit être après la date de début.');
        }

        $nbJours = (int) $start->diff($end)->days + 1;
        $userId = (int) session('user_id');

        $db = \Config\Database::connect();
        $typeConge = $db->table('types_conge')
            ->select('id')
            ->where('id', $typeCongeId)
            ->get()
            ->getRowArray();

        if (! $typeConge) {
            return redirect()->back()->withInput()->with('error', 'Type de congé invalide.');
        }

        $db->table('conges')->insert([
            'employe_id' => $userId,
            'type_conge_id' => $typeCongeId,
            'date_debut' => $dateDebut,
            'date_fin' => $dateFin,
            'nb_jours' => $nbJours,
            'motif' => $motif !== '' ? $motif : null,
            'statut' => 'en_attente',
        ]);

        return redirect()->to(route_to('dashboard'))
            ->with('success', 'Votre demande de congé a bien été soumise.');
    }

    public function profilEmploye()
    {
        $role = session('user_role');
        if (! session('user_id')) {
            return redirect()->to(route_to('login'));
        }
        if ($role === 'admin') {
            return redirect()->to(route_to('dashboard.admin'));
        }
        if ($role === 'rh') {
            return redirect()->to(route_to('dashboard.rh'));
        }

        $userId = (int) session('user_id');
        $db = \Config\Database::connect();

        $employe = $db->table('employes')
            ->select('employes.*, departements.nom AS departement')
            ->join('departements', 'departements.id = employes.departement_id', 'left')
            ->where('employes.id', $userId)
            ->get()
            ->getRowArray();

        if (! $employe) {
            return redirect()->to(route_to('dashboard'));
        }

        $userName = trim($employe['prenom'] . ' ' . $employe['nom']);
        $userRole = $employe['role'] ?? 'employe';
        $userEmail = $employe['email'] ?? '';
        $displayRole = $userRole === 'admin'
            ? 'Administrateur'
            : ($userRole === 'rh' ? 'Responsable RH' : 'Employé');

        $initials = '';
        foreach (preg_split('/\s+/', trim($userName)) as $part) {
            if ($part !== '') {
                $initials .= strtoupper(substr($part, 0, 1));
            }
        }
        $initials = $initials !== '' ? $initials : 'EM';

        return view('pages/profil_employe', [
            'userName' => $userName,
            'userRole' => $userRole,
            'userEmail' => $userEmail,
            'displayRole' => $displayRole,
            'initials' => $initials,
            'departement' => $employe['departement'] ?? null,
            'dateEmbauche' => $employe['date_embauche'] ?? null,
        ]);
    }

    public function updatePassword()
    {
        $role = session('user_role');
        if (! session('user_id')) {
            return redirect()->to(route_to('login'));
        }
        if ($role === 'admin') {
            return redirect()->to(route_to('dashboard.admin'));
        }
        if ($role === 'rh') {
            return redirect()->to(route_to('dashboard.rh'));
        }

        $rules = [
            'current_password' => 'required|min_length[4]',
            'new_password' => 'required|min_length[6]',
            'confirm_password' => 'required|matches[new_password]'
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Veuillez vérifier les champs du mot de passe.');
        }

        $userId = (int) session('user_id');
        $current = (string) $this->request->getPost('current_password');
        $new = (string) $this->request->getPost('new_password');

        $db = \Config\Database::connect();
        $employe = $db->table('employes')
            ->select('id, password')
            ->where('id', $userId)
            ->get()
            ->getRowArray();

        if (! $employe || ! password_verify($current, $employe['password'])) {
            return redirect()->back()->withInput()->with('error', 'Mot de passe actuel incorrect.');
        }

        $db->table('employes')
            ->where('id', $userId)
            ->update(['password' => password_hash($new, PASSWORD_BCRYPT)]);

        return redirect()->to(route_to('profil.employe'))->with('success', 'Mot de passe mis à jour.');
    }
}
