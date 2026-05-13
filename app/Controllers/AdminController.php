<?php

namespace App\Controllers;

class AdminController extends BaseController
{
    private function requireAdmin()
    {
        if (! session('user_id')) {
            return redirect()->to(route_to('login'));
        }
        if (session('user_role') !== 'admin') {
            return redirect()->to(route_to('dashboard'));
        }
        return null;
    }

    private function adminContext(array $extra = []): array
    {
        $userName = session('user_name') ?? 'Administrateur';
        $userEmail = session('user_email') ?? '';
        $initials = '';
        foreach (preg_split('/\s+/', trim($userName)) as $part) {
            if ($part !== '') {
                $initials .= strtoupper(substr($part, 0, 1));
            }
        }
        $initials = $initials !== '' ? $initials : 'AD';

        return array_merge([
            'userName' => $userName,
            'userEmail' => $userEmail,
            'initials' => $initials,
        ], $extra);
    }

    public function dashboard()
    {
        if ($redirect = $this->requireAdmin()) {
            return $redirect;
        }

        $db = \Config\Database::connect();
        $today = date('Y-m-d');
        $monthStart = date('Y-m-01');
        $monthEnd = date('Y-m-t');

        $stats = [
            'employes_actifs' => (int) $db->table('employes')->where('actif', 1)->countAllResults(),
            'demandes_attente' => (int) $db->table('conges')->where('statut', 'en_attente')->countAllResults(),
            'approuvees_mois' => (int) $db->table('conges')
                ->where('statut', 'approuvee')
                ->where('date_debut >=', $monthStart)
                ->where('date_debut <=', $monthEnd)
                ->countAllResults(),
            'departements' => (int) $db->table('departements')->countAllResults(),
            'absents_aujourdhui' => (int) $db->table('conges')
                ->where('statut', 'approuvee')
                ->where('date_debut <=', $today)
                ->where('date_fin >=', $today)
                ->countAllResults(),
        ];

        $recentDemandes = $db->table('conges c')
            ->select('c.nb_jours, c.statut, tc.libelle AS type, e.prenom, e.nom')
            ->join('types_conge tc', 'tc.id = c.type_conge_id', 'left')
            ->join('employes e', 'e.id = c.employe_id', 'left')
            ->orderBy('c.created_at', 'DESC')
            ->limit(5)
            ->get()
            ->getResultArray();

        return view('pages/dasboard_admin', $this->adminContext([
            'stats' => $stats,
            'recentDemandes' => $recentDemandes,
        ]));
    }

    public function employesIndex()
    {
        if ($redirect = $this->requireAdmin()) {
            return $redirect;
        }

        $db = \Config\Database::connect();
        $year = (int) date('Y');

        $departements = $db->table('departements')->orderBy('nom', 'ASC')->get()->getResultArray();

        $employes = $db->table('employes e')
            ->select('e.*, d.nom AS departement')
            ->join('departements d', 'd.id = e.departement_id', 'left')
            ->orderBy('e.nom', 'ASC')
            ->get()
            ->getResultArray();

        $annuelType = $db->table('types_conge')->select('id')->where('libelle', 'Congé annuel')->get()->getRowArray();
        $annuelId = $annuelType['id'] ?? null;

        $soldesAnnuel = [];
        if ($annuelId) {
            $rows = $db->table('soldes')
                ->select('employe_id, jours_attribues, jours_pris')
                ->where('type_conge_id', $annuelId)
                ->where('annee', $year)
                ->get()
                ->getResultArray();
            foreach ($rows as $row) {
                $soldesAnnuel[$row['employe_id']] = $row;
            }
        }

        return view('pages/gestion_employe', $this->adminContext([
            'departements' => $departements,
            'employes' => $employes,
            'soldesAnnuel' => $soldesAnnuel,
            'year' => $year,
        ]));
    }

    public function employeStore()
    {
        if ($redirect = $this->requireAdmin()) {
            return $redirect;
        }

        $rules = [
            'prenom' => 'required|min_length[2]',
            'nom' => 'required|min_length[2]',
            'email' => 'required|valid_email',
            'password' => 'required|min_length[6]',
            'role' => 'required|in_list[employe,rh,admin]',
            'departement_id' => 'permit_empty|is_natural_no_zero',
            'date_embauche' => 'permit_empty|valid_date',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Veuillez vérifier les champs.');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $db->table('employes')->insert([
            'prenom' => $this->request->getPost('prenom'),
            'nom' => $this->request->getPost('nom'),
            'email' => $this->request->getPost('email'),
            'password' => password_hash((string) $this->request->getPost('password'), PASSWORD_BCRYPT),
            'role' => $this->request->getPost('role'),
            'departement_id' => $this->request->getPost('departement_id') ?: null,
            'date_embauche' => $this->request->getPost('date_embauche') ?: null,
            'actif' => 1,
        ]);

        $employeId = $db->insertID();
        $year = (int) date('Y');
        $types = $db->table('types_conge')->select('id, jours_annuels')->get()->getResultArray();
        foreach ($types as $type) {
            $db->table('soldes')->insert([
                'employe_id' => $employeId,
                'type_conge_id' => $type['id'],
                'annee' => $year,
                'jours_attribues' => (int) $type['jours_annuels'],
                'jours_pris' => 0,
            ]);
        }

        $db->transComplete();

        return redirect()->to(route_to('admin.employes'))->with('success', 'Employé créé.');
    }

    public function employeToggle($id)
    {
        if ($redirect = $this->requireAdmin()) {
            return $redirect;
        }

        $db = \Config\Database::connect();
        $employe = $db->table('employes')->select('id, actif')->where('id', (int) $id)->get()->getRowArray();
        if (! $employe) {
            return redirect()->back()->with('error', 'Employé introuvable.');
        }

        $db->table('employes')
            ->where('id', (int) $id)
            ->update(['actif' => $employe['actif'] ? 0 : 1]);

        return redirect()->back()->with('success', 'Statut employé mis à jour.');
    }

    public function departementsIndex()
    {
        if ($redirect = $this->requireAdmin()) {
            return $redirect;
        }

        $db = \Config\Database::connect();
        $departements = $db->table('departements')->orderBy('nom', 'ASC')->get()->getResultArray();

        return view('pages/admin_departements', $this->adminContext([
            'departements' => $departements,
        ]));
    }

    public function departementStore()
    {
        if ($redirect = $this->requireAdmin()) {
            return $redirect;
        }

        $rules = [
            'nom' => 'required|min_length[2]'
        ];
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Nom de département requis.');
        }

        $db = \Config\Database::connect();
        $db->table('departements')->insert([
            'nom' => $this->request->getPost('nom'),
            'description' => $this->request->getPost('description') ?: null,
        ]);

        return redirect()->to(route_to('admin.departements'))->with('success', 'Département ajouté.');
    }

    public function departementDelete($id)
    {
        if ($redirect = $this->requireAdmin()) {
            return $redirect;
        }

        $db = \Config\Database::connect();
        $db->table('departements')->where('id', (int) $id)->delete();

        return redirect()->back()->with('success', 'Département supprimé.');
    }

    public function typesIndex()
    {
        if ($redirect = $this->requireAdmin()) {
            return $redirect;
        }

        $db = \Config\Database::connect();
        $types = $db->table('types_conge')->orderBy('libelle', 'ASC')->get()->getResultArray();

        return view('pages/admin_types_conge', $this->adminContext([
            'types' => $types,
        ]));
    }

    public function typeStore()
    {
        if ($redirect = $this->requireAdmin()) {
            return $redirect;
        }

        $rules = [
            'libelle' => 'required|min_length[2]',
            'jours_annuels' => 'required|integer',
            'deductible' => 'required|in_list[0,1]'
        ];
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Veuillez vérifier les champs.');
        }

        $db = \Config\Database::connect();
        $db->table('types_conge')->insert([
            'libelle' => $this->request->getPost('libelle'),
            'jours_annuels' => (int) $this->request->getPost('jours_annuels'),
            'deductible' => (int) $this->request->getPost('deductible'),
        ]);

        return redirect()->to(route_to('admin.types'))->with('success', 'Type de congé ajouté.');
    }

    public function typeDelete($id)
    {
        if ($redirect = $this->requireAdmin()) {
            return $redirect;
        }

        $db = \Config\Database::connect();
        $db->table('types_conge')->where('id', (int) $id)->delete();

        return redirect()->back()->with('success', 'Type supprimé.');
    }

    public function soldesIndex()
    {
        if ($redirect = $this->requireAdmin()) {
            return $redirect;
        }

        $db = \Config\Database::connect();
        $year = (int) date('Y');

        $employes = $db->table('employes')->select('id, prenom, nom')->orderBy('nom', 'ASC')->get()->getResultArray();
        $types = $db->table('types_conge')->select('id, libelle')->orderBy('libelle', 'ASC')->get()->getResultArray();

        $soldes = $db->table('soldes s')
            ->select('s.*, e.prenom, e.nom, tc.libelle')
            ->join('employes e', 'e.id = s.employe_id')
            ->join('types_conge tc', 'tc.id = s.type_conge_id')
            ->orderBy('e.nom', 'ASC')
            ->get()
            ->getResultArray();

        return view('pages/admin_soldes', $this->adminContext([
            'employes' => $employes,
            'types' => $types,
            'soldes' => $soldes,
            'year' => $year,
        ]));
    }

    public function soldeStore()
    {
        if ($redirect = $this->requireAdmin()) {
            return $redirect;
        }

        $rules = [
            'employe_id' => 'required|is_natural_no_zero',
            'type_conge_id' => 'required|is_natural_no_zero',
            'annee' => 'required|integer',
            'jours_attribues' => 'required|integer',
            'jours_pris' => 'required|integer',
        ];
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Veuillez vérifier les champs.');
        }

        $db = \Config\Database::connect();
        $employeId = (int) $this->request->getPost('employe_id');
        $typeId = (int) $this->request->getPost('type_conge_id');
        $annee = (int) $this->request->getPost('annee');

        $solde = $db->table('soldes')
            ->select('id')
            ->where('employe_id', $employeId)
            ->where('type_conge_id', $typeId)
            ->where('annee', $annee)
            ->get()
            ->getRowArray();

        $data = [
            'employe_id' => $employeId,
            'type_conge_id' => $typeId,
            'annee' => $annee,
            'jours_attribues' => (int) $this->request->getPost('jours_attribues'),
            'jours_pris' => (int) $this->request->getPost('jours_pris'),
        ];

        if ($solde) {
            $db->table('soldes')->where('id', $solde['id'])->update($data);
        } else {
            $db->table('soldes')->insert($data);
        }

        return redirect()->to(route_to('admin.soldes'))->with('success', 'Solde mis à jour.');
    }

    public function historiqueIndex()
    {
        if ($redirect = $this->requireAdmin()) {
            return $redirect;
        }

        $db = \Config\Database::connect();
        $demandes = $db->table('conges c')
            ->select('c.date_debut, c.date_fin, c.nb_jours, c.statut, tc.libelle AS type, e.prenom, e.nom')
            ->join('types_conge tc', 'tc.id = c.type_conge_id', 'left')
            ->join('employes e', 'e.id = c.employe_id', 'left')
            ->orderBy('c.created_at', 'DESC')
            ->get()
            ->getResultArray();

        return view('pages/admin_historique', $this->adminContext([
            'demandes' => $demandes,
        ]));
    }
}
