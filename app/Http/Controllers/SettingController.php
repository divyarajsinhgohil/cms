<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SiteSetting;
use App\Services\WhatsAppService;

class SettingController extends Controller
{
    public function edit()
    {
        $settings = SiteSetting::current();

        return view('settings.edit', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'site_name' => 'required|string|max:255',
            'affiliation' => 'nullable|string|max:255',
            'logo_url' => 'nullable|string|max:500',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'academic_year' => 'required|string|max:20',
            'whatsapp_access_token' => 'nullable|string|max:1000',
            'whatsapp_phone_number_id' => 'nullable|string|max:100',
            'whatsapp_business_account_id' => 'nullable|string|max:100',
        ]);

        SiteSetting::current()->update($validated);

        return redirect()->back()->with('success', 'Website settings updated successfully!');
    }

    public function checkWhatsApp(WhatsAppService $whatsapp)
    {
        return response()->json($whatsapp->checkConnection());
    }
}
