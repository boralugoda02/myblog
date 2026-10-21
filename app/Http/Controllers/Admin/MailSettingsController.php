<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\TestMail;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class MailSettingsController extends Controller
{
    public function edit()
    {
        $settings = Setting::group('mail');
        $hasPassword = ! empty($settings['password']);
        unset($settings['password']);

        return view('admin.mail-settings', compact('settings', 'hasPassword'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'host'         => 'required|string|max:255',
            'port'         => 'required|integer|between:1,65535',
            'encryption'   => 'required|in:tls,ssl',
            'username'     => 'nullable|string|max:255',
            'password'     => 'nullable|string|max:255',
            'from_address' => 'required|email|max:255',
            'from_name'    => 'required|string|max:100',
        ]);

        foreach (['host', 'port', 'encryption', 'username', 'from_address', 'from_name'] as $key) {
            Setting::setValue('mail.' . $key, $data[$key] ?? null);
        }

        
        if (! empty($data['password'])) {
            Setting::setValue('mail.password', Crypt::encryptString($data['password']));
        }

        return back()->with('success', 'Mail settings saved.');
    }

    public function test(Request $request)
    {
        $request->validate([
            'test_email' => 'required|email',
        ]);

        $s = Setting::group('mail');

        if (empty($s['host'])) {
            return back()->with('error', 'Save the mail settings first, then send a test.');
        }

        
        $password = null;
        if (! empty($s['password'])) {
            try {
                $password = Crypt::decryptString($s['password']);
            } catch (\Throwable $e) {
                return back()->with('error', 'Saved password could not be read. Please enter the password again and save.');
            }
        }

        $encryption = $s['encryption'] ?? 'tls';

        
        config([
            'mail.default'               => 'smtp',
            'mail.mailers.smtp.host'     => $s['host'],
            'mail.mailers.smtp.port'     => (int) ($s['port'] ?? 587),
            'mail.mailers.smtp.username' => $s['username'] ?? null,
            'mail.mailers.smtp.password' => $password,
            'mail.mailers.smtp.scheme'   => $encryption === 'ssl' ? 'smtps' : 'smtp',
            'mail.from.address'          => $s['from_address'] ?? config('mail.from.address'),
            'mail.from.name'             => $s['from_name'] ?? config('mail.from.name'),
        ]);

        Mail::purge('smtp'); 

        try {
            Mail::to($request->test_email)->send(new TestMail(auth()->user()->name ?? 'User', $request->test_email));
        } catch (\Throwable $e) {
            return back()
                ->withInput()
                ->with('error', 'The email could not be sent: ' . Str::limit($e->getMessage(), 300));
        }

        return back()->with('success', 'Test email sent to ' . $request->test_email . ' (' . strtoupper($encryption) . '). Check the inbox (and spam folder).');
    }
}