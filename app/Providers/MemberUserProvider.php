<?php

namespace App\Providers;

use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Auth\Authenticatable;
use App\Models\Member;

class MemberUserProvider implements UserProvider
{
    public function retrieveByCredentials(array $credentials)
    {
        // Lấy thông tin người dùng từ bảng `member` bằng email
        return Member::where('email', $credentials['email'])->first();
    }

    public function createUser($googleUser)
    {
        // Tạo mới người dùng trong bảng `member` từ thông tin Google
        return Member::create([
            'name' => $googleUser['name'],
            'email' => $googleUser['email'],
            'coin' => 30,
            // Thêm các trường khác nếu cần
        ]);
    }

    public function retrieveById($identifier)
    {
        // TODO: Implement retrieveById() method.
    }

    public function retrieveByToken($identifier, $token)
    {
        // TODO: Implement retrieveByToken() method.
    }

    public function updateRememberToken(Authenticatable $user, $token)
    {
        // TODO: Implement updateRememberToken() method.
    }

    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        // TODO: Implement validateCredentials() method.
    }
}
