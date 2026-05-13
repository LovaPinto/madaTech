<?php

namespace App\Models;

use CodeIgniter\Model;

class DepartementModel extends Model
{
    protected $table            = 'departements';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['nom', 'description'];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'nom' => 'required|min_length[2]|max_length[255]',
        'description' => 'permit_empty|max_length[500]',
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    // ========== MÉTHODES CRUD ==========

    /**
     * Récupère tous les départements
     * @return array
     */
    public function getDepartements(): array
    {
        return $this->findAll();
    }

    /**
     * Récupère un département par son ID
     * @param int $id
     * @return array|null
     */
    public function getDepartementById(int $id): ?array
    {
        return $this->find($id);
    }

    /**
     * Crée un nouveau département
     * @param array $data
     * @return int|false ID inséré ou false en cas d'échec
     */
    public function createDepartement(array $data): int|false
    {
        return $this->insert($data);
    }

    /**
     * Met à jour un département existant
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function updateDepartement(int $id, array $data): bool
    {
        return $this->update($id, $data);
    }

    /**
     * Supprime un département
     * @param int $id
     * @return bool
     */
    public function deleteDepartement(int $id): bool
    {
        return $this->delete($id);
    }
}