<?php

namespace App\Controllers;

use App\Models\DepartementModel;
use CodeIgniter\Controller;
use CodeIgniter\HTTP\ResponseInterface;

class DepartementController extends Controller
{
    protected $departementModel;
    protected $session;

    public function __construct()
    {
        $this->departementModel = new DepartementModel();
        $this->session = \Config\Services::session();
    }

    /**
     * Affiche la liste des départements (vue HTML)
     * @return string
     */
    public function index()
    {
        $data['departements'] = $this->departementModel->getDepartements();
        return view('departement/index', $data);
    }

    /**
     * Retourne la liste des départements au format JSON (pour API / AJAX)
     * @return ResponseInterface
     */
    public function list()
    {
        $departements = $this->departementModel->getDepartements();
        return $this->response->setJSON($departements);
    }

    /**
     * Gère la création d'un département (formulaire + insertion)
     * @return mixed
     */
    public function create()
    {
        // Vérifier si la requête est de type POST
        if ($this->request->getMethod() === 'POST') {
            // Règles de validation
            $rules = [
                'nom' => 'required|min_length[2]|max_length[255]',
                'description' => 'permit_empty|max_length[500]'
            ];

            if (!$this->validate($rules)) {
                // Retourner le formulaire avec les erreurs
                return view('departement/create', [
                    'validation' => $this->validator,
                    'oldInput' => $this->request->getPost()
                ]);
            }

            // Préparer les données
            $data = [
                'nom' => $this->request->getPost('nom'),
                'description' => $this->request->getPost('description')
            ];

            // Insérer via le modèle
            $insertedId = $this->departementModel->createDepartement($data);

            if ($insertedId) {
                $this->session->setFlashdata('success', 'Département créé avec succès.');
                return redirect()->to('/departement');
            } else {
                $this->session->setFlashdata('error', 'Erreur lors de la création du département.');
                return redirect()->back()->withInput();
            }
        }

        // Afficher le formulaire (GET)
        return view('departement/create');
    }

    // Optionnel : méthode pour voir/modifier/supprimer un département spécifique
    // public function edit($id) { ... }
    // public function update($id) { ... }
    // public function delete($id) { ... }

        /**
     * Supprime un département
     * @param int $id
     * @return mixed
     */
    public function delete($id)
    {
        $departement = $this->departementModel->getDepartementById($id);
        if (!$departement) {
            $this->session->setFlashdata('error', 'Département non trouvé.');
            return redirect()->to('/departement');
        }

        $deleted = $this->departementModel->deleteDepartement($id);

        if ($deleted) {
            $this->session->setFlashdata('success', 'Département supprimé avec succès.');
        } else {
            $this->session->setFlashdata('error', 'Erreur lors de la suppression.');
        }

        return redirect()->to('/departement');
    }


    /**
     * Met à jour un département
     * @param int $id
     * @return mixed
     */
    public function update($id)
    {
        // Vérifier si le département existe
        $departement = $this->departementModel->getDepartementById($id);
        if (!$departement) {
            $this->session->setFlashdata('error', 'Département non trouvé.');
            return redirect()->to('/departement');
        }

        if ($this->request->getMethod() === 'POST') {
            $rules = [
                'nom' => 'required|min_length[2]|max_length[255]',
                'description' => 'permit_empty|max_length[500]'
            ];

            if (!$this->validate($rules)) {
                return view('departement/edit', [
                    'departement' => $departement,
                    'validation' => $this->validator,
                    'oldInput' => $this->request->getPost()
                ]);
            }

            $data = [
                'nom' => $this->request->getPost('nom'),
                'description' => $this->request->getPost('description')
            ];

            $updated = $this->departementModel->updateDepartement($id, $data);

            if ($updated) {
                $this->session->setFlashdata('success', 'Département mis à jour avec succès.');
                return redirect()->to('/departement');
            } else {
                $this->session->setFlashdata('error', 'Erreur lors de la mise à jour.');
                return redirect()->back()->withInput();
            }
        }

        // Si ce n'est pas POST, rediriger vers la liste
        return redirect()->to('/departement');
    }
}