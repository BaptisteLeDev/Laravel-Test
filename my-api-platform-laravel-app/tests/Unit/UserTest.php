<?php

namespace Tests\Unit;

use App\Models\User;
use Tests\TestCase;

class UserTest extends TestCase
{
    public function test_is_admin_returns_true_when_name_is_admin(): void
    {
        $user = new User(['name' => 'admin']);

        $this->assertTrue($user->isAdmin());
    }

    public function test_is_admin_returns_false_when_name_is_not_admin(): void
    {
        $user = new User(['name' => 'prof-demo']);

        $this->assertFalse($user->isAdmin());
    }
}
