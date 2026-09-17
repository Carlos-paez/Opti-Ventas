<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Validator;
use App\Core\View;
use App\Models\Setting;

final class SettingController
{
    private const SETTING_KEYS = ['business_name', 'tax_rate', 'currency', 'receipt_footer'];

    public function index(): void
    {
        View::render('settings/index', [
            'settings' => Setting::allKeys(self::SETTING_KEYS),
        ]);
    }

    public function update(): void
    {
        $data = [
            'business_name' => trim((string) input('business_name')),
            'tax_rate' => input('tax_rate'),
            'currency' => trim((string) input('currency')),
            'receipt_footer' => trim((string) input('receipt_footer')),
        ];

        $errors = Validator::validate($data, [
            'business_name' => 'required|string|max:255',
            'tax_rate' => 'required|numeric|min:0|max:100',
            'currency' => 'required|string|max:10',
            'receipt_footer' => 'nullable|string|max:500',
        ]);

        if ($errors) {
            flash_errors($errors);
            old_input($data);
            redirect('/settings');
        }

        foreach ($data as $key => $value) {
            Setting::set($key, (string) $value);
        }

        flash('success', 'Configuración guardada correctamente.');
        redirect('/settings');
    }
}