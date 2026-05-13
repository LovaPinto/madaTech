<?php

namespace App\Models;

use CodeIgniter\Model;

class EmployeModel extends Model
{
    protected $table            = 'employes';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;

    // Champs autorisés pour INSERT/UPDATE
    protected $allowedFields    = [
        'nom',
        'prenom',
        'email',
        'password',
        'role',
        'departement_id',
        'date_embauche',
        'actif'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    // Casts pour correspondre aux types SQLite
    protected array $casts = [
        'actif' => 'boolean',
        'departement_id' => 'integer'
    ];

    protected array $castHandlers = [];

    // Dates (pas de created_at/updated_at dans cette table)
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = '';
    protected $updatedField  = '';
    protected $deletedField  = '';

    // Validation (à compléter si besoin)
    protected $validationRules      = [
        'email' => 'required|valid_email',
        'nom'   => 'required|min_length[2]',
        'prenom'=> 'required|min_length[2]',
        'role'  => 'in_list[employe,rh,admin]'
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
}
