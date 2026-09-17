<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Validator;
use App\Core\View;
use App\Models\User;

final class UserController
{
    public function index(): void
    {
        $page = max(1, (int) query('page', 1));
        $filters = [
            'search' => trim((string) query('search', '')),
            'role' => query('role', '') !== '' ? (string) query('role') : null,
        ];

        $paged = User::all($filters, $page, 15);

        View::render('users/index', [
            'users' => $paged['items'],
            'total' => $paged['total'],
            'page' => $page,
            'perPage' => 15,
            'filters' => array_merge($filters, ['role' => $filters['role'] ?? '']),
        ]);
    }

    public function create(): void
    {
        View::render('users/create');
    }

    public function store(): void
    {
        $data = [
            'name' => trim((string) input('name')),
            'email' => trim((string) input('email')),
            'role' => trim((string) input('role')),
            'password' => (string) input('password'),
            'password_confirmation' => (string) input('password_confirmation'),
        ];

        $errors = Validator::validate($data, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'role' => 'required|in:admin,seller',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($errors) {
            flash_errors($errors);
            old_input($data);
            redirect('/users/create');
        }

        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => password_hash($data['password'], PASSWORD_DEFAULT),
            'role' => $data['role'],
            'is_active' => 1,
        ]);

        flash('success', 'Usuario creado correctamente.');
        redirect('/users');
    }

    public function edit(int $id): void
    {
        $user = User::find($id);

        if ($user === null) {
            flash('error', 'El usuario no existe.');
            redirect('/users');
        }

        View::render('users/edit', ['user' => $user]);
    }

    public function update(int $id): void
    {
        $user = User::find($id);

        if ($user === null) {
            flash('error', 'El usuario no existe.');
            redirect('/users');
        }

        $data = [
            'name' => trim((string) input('name')),
            'email' => trim((string) input('email')),
            'role' => trim((string) input('role')),
            'password' => (string) input('password'),
            'password_confirmation' => (string) input('password_confirmation'),
        ];

        $errors = Validator::validate($data, [
            'name' => 'required|string|max:255',
            'email' => "required|string|email|max:255|unique:users,email,{$id}",
            'role' => 'required|in:admin,seller',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        if ($errors) {
            flash_errors($errors);
            old_input($data);
            redirect('/users/' . $id . '/edit');
        }

        $update = [
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $data['role'],
        ];

        if ($data['password'] !== '') {
            $update['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        User::update($id, $update);

        flash('success', 'Usuario actualizado correctamente.');
        redirect('/users');
    }

    public function toggleActive(int $id): void
    {
        if ($id === Auth::id()) {
            flash('error', 'No puedes desactivar tu propio usuario.');
            back();
        }

        User::toggleActive($id);

        $user = User::find($id);
        flash('success', $user !== null && (int) $user['is_active'] ? 'Usuario activado.' : 'Usuario desactivado.');
        back();
    }
}