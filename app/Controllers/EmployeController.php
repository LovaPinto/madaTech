<?php

namespace App\Controllers;

use App\Models\EmployeModel;
use CodeIgniter\Controller;

class EmployeController extends BaseController
{
    protected $employeModel;

    public function __construct()
    {
        $this->employeModel = new EmployeModel();
    }

    // Liste des employés
    public function index()
    {
        $data['employes'] = $this->employeModel->findAll();
        return view('employes/index', $data);
    }

    // Afficher formulaire de création
    public function create()
    {
        return view('employes/create');
    }

    // Enregistrer un nouvel employé
    public function store()
    {
        $this->employeModel->save([
            'nom'            => $this->request->getPost('nom'),
            'prenom'         => $this->request->getPost('prenom'),
            'email'          => $this->request->getPost('email'),
            'password'       => password_hash($this->request->getPost('password'), PASSWORD_BCRYPT),
            'role'           => $this->request->getPost('role'),
            'departement_id' => $this->request->getPost('departement_id'),
            'date_embauche'  => $this->request->getPost('date_embauche'),
            'actif'          => $this->request->getPost('actif') ?? 1,
        ]);

        return redirect()->to('/employes');
    }

    // Afficher formulaire d’édition
    public function edit($id)
    {
        $data['employe'] = $this->employeModel->find($id);
        return view('employes/edit', $data);
    }

    // Mettre à jour un employé
    public function update($id)
    {
        $this->employeModel->update($id, [
            'nom'            => $this->request->getPost('nom'),
            'prenom'         => $this->request->getPost('prenom'),
            'email'          => $this->request->getPost('email'),
            // On ne change le mot de passe que si fourni
            'password'       => $this->request->getPost('password') 
                                ? password_hash($this->request->getPost('password'), PASSWORD_BCRYPT) 
                                : $this->employeModel->find($id)['password'],
            'role'           => $this->request->getPost('role'),
            'departement_id' => $this->request->getPost('departement_id'),
            'date_embauche'  => $this->request->getPost('date_embauche'),
            'actif'          => $this->request->getPost('actif') ?? 1,
        ]);

        return redirect()->to('/employes');
    }

    // Supprimer un employé
    public function delete($id)
    {
        $this->employeModel->delete($id);
        return redirect()->to('/employes');
    }

    // Détails d’un employé
    public function show($id)
    {
        $data['employe'] = $this->employeModel->find($id);
        return view('employes/show', $data);
    }
}
