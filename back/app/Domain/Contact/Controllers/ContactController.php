<?php

namespace App\Domain\Contact\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class ContactController extends Controller
{
    public function send(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'message' => ['required', 'string', 'max:999'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();
        $to = config('fedex.parcel_options.recipient_email', 'hello@goldtocash.us')
            ?: 'hello@goldtocash.us';

        $viewData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'body' => $data['message'],
        ];

        try {
            Mail::send('emails.contact', $viewData, function ($mailMessage) use ($data, $to) {
                $mailMessage->to($to)
                    ->replyTo($data['email'], $data['name'])
                    ->subject('Contact form: ' . $data['name']);
            });
        } catch (\Throwable $e) {
            Log::error('Contact form mail failed', [
                'error' => $e->getMessage(),
                'email' => $data['email'],
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Sorry, something went wrong. Please try again later or email hello@goldtocash.us.',
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Your message has been sent. We will get back to you soon!',
        ]);
    }
}
