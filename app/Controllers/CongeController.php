<?php

namespace App\Controllers;

use App\Models\CongeModel;
use CodeIgniter\Controller;

class CongeController extends Controller
{
    protected $congeModel;

    public function __construct()
    {
        $this->congeModel = new CongeModel();
    }

    public function create()
    {
        // Récupération des données du formulaire
        $data = [
            'employe_id'    => $this->request->getPost('employe_id'),
            'type_conge_id' => $this->request->getPost('type_conge_id'),
            'date_debut'    => $this->request->getPost('date_debut'),
            'date_fin'      => $this->request->getPost('date_fin'),
            'nb_jours'      => $this->request->getPost('nb_jours'),
            'motif'         => $this->request->getPost('motif'),
            'statut'        => 'en_attente', // par défaut
            'traite_par'    => null,
        ];

        $this->congeModel->insert($data);

        return redirect()->to('/mes-demandes');
    }

    public function listByEmploye($employeId)
    {
        $demandes = $this->congeModel
            ->where('employe_id', $employeId)
            ->orderBy('created_at', 'DESC')
            ->findAll();

        return view('pages/liste', ['demandes' => $demandes]);
    }


    public function lastThree($employeId)
    {
        $demandes = $this->congeModel
            ->where('employe_id', $employeId)
            ->orderBy('created_at', 'DESC')
            ->limit(3)
            ->find();

        return view('pages/dernieres', ['demandes' => $demandes]);
    }
}
