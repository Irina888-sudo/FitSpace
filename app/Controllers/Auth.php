<?php

namespace App\Controllers;

class Auth extends BaseController
{
    public function login()
    {
        return view('login');
    }

    public function register()
    {
        return view('register');
    }

    public function creneauxClient()
    {
        return view('creneaux-Client');
    }

    public function creneauxAdmin()
    {
        return view('creneaux-Admin');
    }

    public function createcount()
    {
        $model = new UserModel();
        $data = [
            'nom' => $this->request->getPost('nom'),
            'prenom' => $this->request->getPost('prenom'),
            'email' => $this->request->getPost('email'),
            'password' => $this->request->getPost('password'),
        ];
        $model->insert($data);
        return redirect()->to('/login');
    }

     public function index() {
        return view('index');
     }
}