<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Validator;
use App\Core\View;
use App\Models\User;

final class AuthController
{
    public function create(): void
    {
        if (Auth::check()) {
            redirect('/dashboard');
        }

        View::render('auth/login', [], 'guest');
    }

    public function store(): void
    {
        $data = [
            'email' => (string) input('email'),
            'password' => (string) input('password'),
        ];

        $errors = Validator::validate($data, [
            'email' => 'required|string|email|max:255',
            'password' => 'required|string',
        ]);

        if (!$errors) {
            $user = User::findByEmail($data['email']);

            if ($user === null || !password_verify($data['password'], (string) $user['password'])) {
                $errors['email'] = 'Las credenciales no coinciden.';
            } elseif (!(int) $user['is_active']) {
                $errors['email'] = 'Tu cuenta está desactivada. Contacta al administrador.';
            }
        }

        if ($errors) {
            flash_errors($errors);
            old_input($data);
            redirect('/login');
        }

        Auth::login($user);
        redirect('/dashboard');
    }

    public function destroy(): void
    {
        Auth::logout();
        redirect('/login');
    }

    public function showRegister(): void
    {
        if (Auth::check()) {
            redirect('/dashboard');
        }

        View::render('auth/register', [], 'guest');
    }

    public function register(): void
    {
        $data = [
            'name' => (string) input('name'),
            'email' => (string) input('email'),
            'password' => (string) input('password'),
            'password_confirmation' => (string) input('password_confirmation'),
        ];

        $errors = Validator::validate($data, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($errors) {
            flash_errors($errors);
            old_input($data);
            redirect('/register');
        }

        $id = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => password_hash($data['password'], PASSWORD_DEFAULT),
            'role' => User::ROLE_SELLER,
            'is_active' => 1,
        ]);

        Auth::login(['id' => $id, 'name' => $data['name']]);
        redirect('/dashboard');
    }
}