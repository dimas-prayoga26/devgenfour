<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateSettingsRequest;
use App\Models\Setting;
use App\Support\ContactDetails;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class SettingController extends Controller
{
    /**
     * Show the contact and social media settings.
     */
    public function edit(): View
    {
        $values = Setting::values(ContactDetails::keys());

        return view('admin.settings.edit', [
            'values' => $values,
            'socialNetworks' => ContactDetails::SOCIAL_NETWORKS,
            'mapEmbedUrl' => (new ContactDetails($values))->mapEmbedUrl(),
        ]);
    }

    /**
     * Save the contact and social media settings; emptied fields are removed.
     */
    public function update(UpdateSettingsRequest $request): RedirectResponse
    {
        Setting::putMany($request->safe()->only(ContactDetails::keys()));

        return to_route('admin.settings.edit')->with('status', 'Pengaturan kontak berhasil disimpan.');
    }
}
