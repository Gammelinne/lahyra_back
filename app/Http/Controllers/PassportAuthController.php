<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class PassportAuthController extends Controller
{
    /**
     * Registration
     */
    public function register(Request $request)
    {
        Log::info($request->all());
        $this->validate($request, [
            'first_name' => 'required|min:4',
            'last_name' => 'required|min:4',
            'pseudo' => 'required|min:4|unique:users',
            'phone' => 'required|min:4|unique:users',
            'gender' => 'required',
            'birthday' => 'required',
            'address' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|max:36|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/', // at least one uppercase, one lowercase and one digit

        ]);
        $recaptha_token = $request->recaptha_token;
        $client = new Client();

        $response = $client->post('https://www.google.com/recaptcha/api/siteverify', [
            'form_params' => [
                'secret' => '6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe',
                'response' => $recaptha_token
            ]
        ]);

        $body = json_decode((string) $response->getBody(), true);
        $body = json_decode(json_encode($body), true);
        if ($body['success'] == true) {
            $user = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'gender' => $request->gender,
                'pseudo' => $request->pseudo,
                'phone' => $request->phone,
                'birthday' => $request->birthday,
                'address' => $request->address,
                'email' => $request->email,
                'password' => Hash::make($request->password, [
                    'rounds' => 12
                ]),
            ]);


            $token = $user->createToken('LaravelAuthApp')->accessToken;
            return response()->json(['success', 'user' => auth()->user(), 'token' => $token], 200);
        } else {
            return response()->json(['error' => 'recaptcha failed'], 401);
        }
    }

    /**
     * Login
     */
    public function login(Request $request)
    {
        $this->validate($request, [
            'email' => 'required',
            'password' => 'required',
        ]);

        $data = [
            'email' => $request->email,
            'password' => $request->password,
        ];
        $recaptha_token = $request->recaptha_token;
        $client = new Client();

        $response = $client->post('https://www.google.com/recaptcha/api/siteverify', [
            'form_params' => [
                'secret' => '6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe',
                'response' => $recaptha_token
            ]
        ]);

        $body = json_decode((string) $response->getBody(), true);
        $body = json_decode(json_encode($body), true);
        if ($body['success'] == true) {
            if (auth()->attempt($data)) {
                $token = auth()->user()->createToken('LaravelAuthApp')->accessToken;
                Log::info('User logged in');
                return response()->json(['success', 'user' => auth()->user(), 'token' => $token], 200);
            } else {
                return response()->json(['error' => 'email or password is wrong'], 401);
            }
        } else {
            return response()->json(['error' => 'recaptha is wrong'], 401);
        }
    }
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }
}
