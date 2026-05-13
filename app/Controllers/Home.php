<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): string
    {
        return view('welcome_message');
    }

    public function dashboard(): string
    {
        return view('pages/dashboard');
    }

    public function dashboardAdmin(): string
    {
        return view('pages/dasboard_admin'); // attention à l’orthographe du fichier
    }

    public function formulaireDemande(): string
    {
        return view('pages/formulaire_demande');
    }

    public function gestionEmploye(): string
    {
        return view('pages/gestion_employe');
    }

    public function listeDemande(): string
    {
        return view('pages/liste_demande');
    }

    public function listeRh(): string
    {
        return view('pages/liste_rh');
    }

    public function login(): string
    {
        return view('pages/login');
    }

    public function etudiantView(): string
    {
        return view('pages/EtudiantView');
    }

    public function templateCongesRh(): string
    {
        return view('pages/template-conges-rh-ci4');
    }
}
