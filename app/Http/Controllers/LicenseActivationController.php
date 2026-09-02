<?php

namespace App\Http\Controllers;

use App\Services\Billing\LicenseService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RuntimeException;

class LicenseActivationController extends Controller
{
    public function create(): View
    {
        return view('license.activate');
    }

    public function store(Request $request, LicenseService $licenses): RedirectResponse
    {
        $validated = $request->validate([
            'license_key' => 'required|string|min:10',
        ]);

        try {
            $licenses->activateForTenant($validated['license_key'], current_tenant());
        } catch (RuntimeException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()->route('dashboard.index')->with('success', 'Licença ativada com sucesso.');
    }
}
