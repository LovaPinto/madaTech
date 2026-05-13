<?php

namespace App\Controllers;

class EtudiantController extends BaseController
{
    public function index(): string
    {
        $datas=["coucou","cloij"];
        return view('EtudiantView',['lists'=>$datas]);
    }
}
