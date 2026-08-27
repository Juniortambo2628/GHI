<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateSettingsRequest;
use App\Models\SiteSetting;
use Illuminate\Http\RedirectResponse;

class SettingsController extends Controller
{
    public function edit()
    {
        return inertia('Admin/Settings/Index', ['settings' => SiteSetting::grouped(config('site_settings'))]);
    }

    public function update(UpdateSettingsRequest $request): RedirectResponse
    {
        foreach ($request->validated() as $key => $value) {
            SiteSetting::updateOrCreate(['key' => $key], ['value' => $value, 'group' => str_contains($key, 'contact') ? 'contact' : 'general']);
        }

        return back()->with('success', 'Settings saved.');
    }
}
