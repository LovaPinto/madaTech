<?php

namespace App\Models;

use CodeIgniter\Model;

class TypeCongeModel extends Model
{
    protected $table            = 'types_conge';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;

    protected $allowedFields    = [
        'libelle',
        'jours_annuels',
        'deductible'
    ];

    protected $validationRules = [
        'libelle'       => 'required|min_length[3]',
        'jours_annuels' => 'integer',
        'deductible'    => 'in_list[0,1]'
    ];
}
