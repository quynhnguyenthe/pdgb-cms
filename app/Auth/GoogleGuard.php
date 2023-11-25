<?php

namespace App\Auth;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard;

class GoogleGuard implements Guard
{
    protected $user;

    public function user()
    {
        return $this->user;
    }

    public function check()
    {
        return !is_null($this->user);
    }

    public function validate(array $credentials = [])
    {
        // Validate the credentials (if needed)
        // Set the authenticated user if valid
        // For simplicity, assume it's always valid in this example
        $this->user = new User(); // Replace with your User model

        return true;
    }

    public function guest()
    {
        // TODO: Implement guest() method.
    }

    public function id()
    {
        // TODO: Implement id() method.
    }

    public function setUser(Authenticatable $user)
    {
        // TODO: Implement setUser() method.
    }
}