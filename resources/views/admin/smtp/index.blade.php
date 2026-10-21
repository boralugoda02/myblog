@extends('layouts.app')

@section('content')
<div style="max-width: 600px; margin: 30px auto; background: #fff; padding: 25px; border-radius: 10px; border: 1px solid #ddd;">
    <h2>SMTP Configuration</h2>

    @if(session('success'))
        <div style="color: green; margin-bottom: 15px;">{{ session('success') }}</div>
    @endif

    <form action="{{ route('admin.smtp.update') }}" method="POST">
        @csrf

        <div style="margin-bottom: 12px;">
            <label>Mail Mailer:</label>
            <input type="text" name="mail_mailer" value="{{ $settings['mail_mailer'] ?? 'smtp' }}" class="form-control" required>
        </div>

        <div style="margin-bottom: 12px;">
            <label>Mail Host:</label>
            <input type="text" name="mail_host" value="{{ $settings['mail_host'] ?? 'smtp.gmail.com' }}" class="form-control" required>
        </div>

        <div style="margin-bottom: 12px;">
            <label>Mail Port:</label>
            <input type="text" name="mail_port" value="{{ $settings['mail_port'] ?? '587' }}" class="form-control" required>
        </div>

        <div style="margin-bottom: 12px;">
            <label>Mail Username (Email):</label>
            <input type="email" name="mail_username" value="{{ $settings['mail_username'] ?? '' }}" class="form-control" required>
        </div>

        <div style="margin-bottom: 12px;">
            <label>Mail App Password:</label>
            <input type="password" name="mail_password" placeholder="පැරණි Password එක තබා ගැනීමට හිස්ව තබන්න" class="form-control">
        </div>

        <div style="margin-bottom: 12px;">
            <label>Mail Encryption:</label>
            <input type="text" name="mail_encryption" value="{{ $settings['mail_encryption'] ?? 'tls' }}" class="form-control">
        </div>

        <div style="margin-bottom: 12px;">
            <label>Mail From Address:</label>
            <input type="email" name="mail_from_address" value="{{ $settings['mail_from_address'] ?? '' }}" class="form-control" required>
        </div>

        <div style="margin-bottom: 12px;">
            <label>Mail From Name:</label>
            <input type="text" name="mail_from_name" value="{{ $settings['mail_from_name'] ?? 'MyBlog' }}" class="form-control" required>
        </div>

        <button type="submit" style="padding: 10px 20px; background: #2563eb; color: #fff; border: none; border-radius: 6px; cursor: pointer;">
            Save Settings
        </button>
    </form>
</div>
@endsection