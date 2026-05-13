<?php

namespace App\Models;

use CodeIgniter\Model;

class CongeModel extends Model
{
    protected $table            = 'conges';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;

    protected $allowedFields    = [
        'employe_id',
        'type_conge_id',
        'date_debut',
        'date_fin',
        'nb_jours',
        'motif',
        'statut',
        'commentaire_rh',
        'created_at',
        'traite_par'
    ];

    protected $validationRules = [
        'employe_id'    => 'required|integer',
        'type_conge_id' => 'required|integer',
        'date_debut'    => 'required|valid_date',
        'date_fin'      => 'required|valid_date',
        'nb_jours'      => 'integer',
        'statut'        => 'in_list[en_attente,approuvee,refusee,annulee]'
    ];
}
