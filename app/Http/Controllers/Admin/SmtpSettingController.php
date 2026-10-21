<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SmtpSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class SmtpSettingController extends Controller
{
    public function index()
    {
        $smtp = SmtpSetting::first();

        return view('admin.smtp.index', compact('smtp'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'host'         => 'required|string|max:255',
            'port'         => 'required|integer|min:1|max:65535',
            'username'     => 'nullable|string|max:255',
            'password'     => 'nullable|string|max:255',
            'encryption'   => 'required|in:tls,ssl,none',
            'from_address' => 'required|email|max:255',
            'from_name'    => 'required|string|max:255',
        ]);

        $data['is_active'] = $request->boolean('is_active');

        $smtp = SmtpSetting::first();

        
        if (empty($data['password'])) {
            unset($data['password']);
        }

        if ($smtp) {
            $smtp->update($data);
        } else {
            SmtpSetting::create($data);
        }

        return redirect()->route('admin.smtp.index')
            ->with('success', 'SMTP settings saved successfully!');
    }

    public function test(Request $request)
    {
        $request->validate([
            'test_email' => 'required|email',
        ]);

        try {
            Mail::raw(
                'This is a test email from MyBlog. Your SMTP settings are working!',
                function ($message) use ($request) {
                    $message->to($request->test_email)->subject('MyBlog SMTP Test');
                }
            );
        } catch (\Throwable $e) {
            return redirect()->route('admin.smtp.index')
                ->with('error', 'Test email failed: ' . $e->getMessage());
        }

        return redirect()->route('admin.smtp.index')
            ->with('success', 'Test email sent to ' . $request->test_email . '. Check your inbox!');
    }
}