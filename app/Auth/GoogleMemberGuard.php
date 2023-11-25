<?php

namespace App\Auth;

use Illuminate\Auth\GuardHelpers;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\UserProvider;
use Google_Client;

class GoogleMemberGuard implements Guard
{
    use GuardHelpers;

    protected $provider;
    protected $client;

    public function __construct(UserProvider $provider, Google_Client $client)
    {
        $this->provider = $provider;
        $this->client = $client;
    }

    public function validate(array $credentials = [])
    {
        // Xác thực thông tin từ token Google
        $this->client->setAccessToken(['access_token' => $credentials['access_token']]);
        try {
            $plus = new \Google\Service\Oauth2($this->client);
            $googleUser = $plus->userinfo->get();
        } catch (\Exception $ex) {
            return false;
        }

        if (!$googleUser) {
            return false;
        }

        // Kiểm tra xem người dùng đã tồn tại trong bảng `member` chưa
        $user = $this->provider->retrieveByCredentials(['email' => $googleUser['email']]);
        if (!$user) {
            // Nếu người dùng chưa tồn tại, tạo mới người dùng trong bảng `member`
            $user = $this->provider->createUser($googleUser);
        }

        $this->setUser($user);

        return true;
    }

    public function user(): ?Authenticatable
    {
        return $this->user;
    }
}