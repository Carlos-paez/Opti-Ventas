<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SettingController extends Controller
{
    private const SETTING_KEYS = ['business_name', 'tax_rate', 'currency', 'receipt_footer'];

    public function index(): View
    {
        $settings = collect(DB::table('settings')->pluck('value', 'key'))
            ->only(self::SETTING_KEYS)
            ->all();

        return view('settings.index', compact('settings'));
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'business_name' => ['required', 'string', 'max:255'],
            'tax_rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'currency' => ['required', 'string', 'max:10'],
            'receipt_footer' => ['nullable', 'string', 'max:500'],
        ]);

        foreach ($validated as $key => $value) {
            DB::table('settings')->updateOrInsert(
                ['key' => $key],
                [
                    'value' => $value,
                    'updated_at' => now(),
                ],
            );
        }

        return redirect()
            ->route('settings.index')
            ->with('success', 'Configuración guardada correctamente.');
    }
}
