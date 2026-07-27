<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ContactController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        // Split full name into first and last
        $names = preg_split('/\s+/', trim($data['name']), 2);
        $firstName = $names[0] ?? '';
        $lastName = $names[1] ?? '';

        $mailData = [
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $data['email'],
            'phone' => $request->input('phone', ''),
            'subject' => $data['subject'],
            'message' => $data['message'],
        ];

        try {
            Mail::send('emails.contact-form', ['data' => $mailData], function ($message) use ($mailData) {
                $to = config('mail.from.address') ?? config('mail.mailers.smtp.username') ?? env('MAIL_TO_ADDRESS');
                if (!$to) {
                    Log::warning('No recipient configured for contact form email.');
                    return;
                }
                $message->to($to)
                    ->subject('Contact Form: ' . ($mailData['subject'] ?? 'New Message'));
            });
        } catch (\Exception $e) {
            Log::error('Failed to send contact form email: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to send message'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json(['message' => 'Message sent successfully'], Response::HTTP_OK);
    }
}
