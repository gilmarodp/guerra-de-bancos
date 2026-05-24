<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\AccessCredentialsMail;
use App\Models\User;
use App\Services\WhatsAppClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AccessCredentialsController extends Controller
{
    public function __invoke(Request $request, WhatsAppClient $whatsApp)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'whatsapp' => ['required', 'string', 'max:30'],
        ]);

        $plainPassword = Str::random(12);

        $existingUser = User::where('email', $data['email'])->first();
        $previousPassword = $existingUser?->password;

        $user = User::updateOrCreate(
            ['email' => $data['email']],
            ['name' => $data['name'], 'password' => $plainPassword]
        );

        try {
            Mail::to($data['email'])->send(new AccessCredentialsMail(
                name: $data['name'],
                emailAddress: $data['email'],
                plainPassword: $plainPassword
            ));

            $whatsApp->sendAccessCredentials(
                name: $data['name'],
                phone: $data['whatsapp'],
                email: $data['email'],
                plainPassword: $plainPassword
            );
        } catch (\Throwable $exception) {
            if ($existingUser) {
                $existingUser->forceFill(['password' => $previousPassword])->save();
            } else {
                $user->delete();
            }

            report($exception);

            return response()->json(['message' => 'Failed to send credentials.'], 500);
        }

        return back()->with('message', 'Access credentials sent successfully.');
    }
}

