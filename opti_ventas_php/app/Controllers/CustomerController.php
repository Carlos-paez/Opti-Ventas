<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Validator;
use App\Core\View;
use App\Models\Customer;

final class CustomerController
{
    public function index(): void
    {
        $page = max(1, (int) query('page', 1));
        $search = trim((string) query('search', ''));

        $paged = Customer::all($search, $page, 15);

        View::render('customers/index', [
            'customers' => $paged['items'],
            'total' => $paged['total'],
            'page' => $page,
            'perPage' => 15,
            'search' => $search,
        ]);
    }

    public function create(): void
    {
        View::render('customers/create');
    }

    public function store(): void
    {
        $data = [
            'name' => trim((string) input('name')),
            'phone' => nullable_string(input('phone')),
            'email' => nullable_string(input('email')),
            'address' => nullable_string(input('address')),
        ];

        $errors = Validator::validate($data, [
            'name' => 'required|string|max:255',
            'email' => 'nullable|string|email|max:255|unique:customers,email',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
        ]);

        if ($errors) {
            flash_errors($errors);
            old_input($data);
            redirect('/customers/create');
        }

        Customer::create($data);

        flash('success', 'Cliente creado correctamente.');
        redirect('/customers');
    }

    public function edit(int $id): void
    {
        $customer = Customer::find($id);

        if ($customer === null) {
            flash('error', 'El cliente no existe.');
            redirect('/customers');
        }

        View::render('customers/edit', ['customer' => $customer]);
    }

    public function update(int $id): void
    {
        $customer = Customer::find($id);

        if ($customer === null) {
            flash('error', 'El cliente no existe.');
            redirect('/customers');
        }

        $data = [
            'name' => trim((string) input('name')),
            'phone' => nullable_string(input('phone')),
            'email' => nullable_string(input('email')),
            'address' => nullable_string(input('address')),
        ];

        $errors = Validator::validate($data, [
            'name' => 'required|string|max:255',
            'email' => "nullable|string|email|max:255|unique:customers,email,{$id}",
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
        ]);

        if ($errors) {
            flash_errors($errors);
            old_input($data);
            redirect('/customers/' . $id . '/edit');
        }

        Customer::update($id, $data);

        flash('success', 'Cliente actualizado correctamente.');
        redirect('/customers');
    }

    public function destroy(int $id): void
    {
        if (Customer::find($id) === null) {
            flash('error', 'El cliente no existe.');
            back();
        }

        if (Customer::hasSales($id)) {
            flash('error', 'No se puede eliminar un cliente que tiene ventas asociadas.');
            back();
        }

        Customer::delete($id);

        flash('success', 'Cliente eliminado correctamente.');
        redirect('/customers');
    }
}