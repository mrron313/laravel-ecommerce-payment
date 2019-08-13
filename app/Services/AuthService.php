<?php

namespace App\Services;

use App\AccountKit;
use App\Repositories\Frontend\Access\User\UserRepository;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class AuthService
{
    /**
     * @param string $code
     *
     * @return string|null
     */
    public function getPhoneNumberFromAccountKit($code)
    {
        
        // check if existing code is given
        $kit = AccountKit::firstOrNew([
            'code' => $code,
            'verified' => true,
        ]);

        if ($kit->exists) {
            return null;
        }

        $appId = '1283114428536031';
        $appSecret = 'e7c9c97e398be06128a5ccef99571d86';
        $appVersion = 'v1.0';

        $http = new Client();
        $tokenExchangeUrl = sprintf(
            'https://graph.accountkit.com/%s/access_token?grant_type=authorization_code&code=%s&access_token=AA|%s|%s',
            $appVersion,
            $code,
            $appId,
            $appSecret
        );
        $response = $http->get($tokenExchangeUrl);
        $data = json_decode((string) $response->getBody(), true);
        $userAccessToken = $data['access_token'];

        // Get Account Kit information
        $appSecretProof= hash_hmac('sha256', $userAccessToken, $appSecret);
        $meEndpointUrl = sprintf(
            'https://graph.accountkit.com/%s/me?access_token=%s&appsecret_proof=%s',
            $appVersion,
            $userAccessToken,
            $appSecretProof
        );
        $response = $http->get($meEndpointUrl);
        $data = json_decode((string) $response->getBody(), true);

        if (! isset($data['phone'])) {
            $kit->save();
            return null;
        }

        $phone = $data['phone']['national_number'];
        $kit->country_prefix = $data['phone']['country_prefix'];
        $kit->number = $phone;
        $kit->save();

        return $phone;
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Repositories\Frontend\Access\User\UserRepository $user
     *
     * @return array
     */
    public function validateUser(Request $request, UserRepository $user)
    {
        $email = $request->input('email');

        if (is_null($phone = $this->getPhoneNumberFromAccountKit($request->input('code')))) {
            Log::error(json_encode([
                'user_id' => null,
                'ip' => $request->ip(),
                'request' => $request->all(),
                'error' => ['phone' => 'The phone number could not be verified.'],
            ]));
            return [
                'status' => false,
                'phone' => '',
                'message' => 'The phone number could not be verified.',
            ];
        }

        // check user existence by phone
        if (! is_null($user->findByPhone($phone))) {
            Log::error(json_encode([
                'user_id' => null,
                'ip' => $request->ip(),
                'request' => $request->all(),
                'error' => ['phone' => 'This phone number is already taken.'],
            ]));
            return [
                'status' => false,
                'phone' => '',
                'message' => 'This phone number is already taken.',
            ];
        }

        // check user existence by email
        if (! is_null($user->findByEmail($email))) {
            Log::error(json_encode([
                'user_id' => null,
                'ip' => $request->ip(),
                'request' => $request->all(),
                'error' => ['email' => 'This email address is already taken.'],
            ]));
            return [
                'status' => false,
                'phone' => '',
                'message' => 'This email address is already taken.',
            ];
        }

        return [
            'status' => true,
            'phone' => $phone,
            'message' => 'User validation is successful.',
        ];
    }
}
