<?php

namespace App\Controllers;

use App\Models\EmployeModel;

class AuthController extends BaseController
{
    protected EmployeModel $employeModel;

    public function __construct()
    {
        $this->employeModel = new EmployeModel();
    }

    public function login()
    {
        helper(['form', 'url']);

        if (session()->get('user_id')) {
            return redirect()->to($this->redirectByRole(session()->get('user_role')));
        }

        return view('pages/login');
    }

    public function attempt()
    {
        helper(['form', 'url']);

        $rules = [
            'email'    => 'required|valid_email',
            'password' => 'required|min_length[4]'
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Veuillez vérifier vos identifiants.');
        }

        $email = (string) $this->request->getPost('email');
        $password = (string) $this->request->getPost('password');

        $employe = $this->employeModel
            ->where('email', $email)
            ->where('actif', 1)
            ->first();

        if (! $employe || ! password_verify($password, $employe['password'])) {
            return redirect()->back()->withInput()->with('error', 'Identifiants incorrects. Veuillez réessayer.');
        }

        session()->set([
            'user_id'    => $employe['id'],
            'user_role'  => $employe['role'],
            'user_email' => $employe['email'],
            'user_name'  => trim($employe['prenom'] . ' ' . $employe['nom'])
        ]);

        return redirect()->to($this->redirectByRole($employe['role']));
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to(route_to('login'));
    }

    private function redirectByRole(?string $role): string
    {
        return match ($role) {
            'admin' => route_to('dashboard.admin'),
            'rh'    => route_to('dashboard.rh'),
            'employe' => route_to('dashboard'),
            default   => route_to('dashboard')
        };
    }
}
