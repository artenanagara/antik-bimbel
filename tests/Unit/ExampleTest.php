<?php

namespace Tests\Unit;

use App\Models\User;
use PHPUnit\Framework\TestCase;

class ExampleTest extends TestCase
{
    public function test_user_role_helpers_identify_admins_and_students(): void
    {
        $admin = new User(['role' => 'admin']);
        $student = new User(['role' => 'student']);

        $this->assertTrue($admin->isAdmin());
        $this->assertFalse($admin->isStudent());
        $this->assertTrue($student->isStudent());
        $this->assertFalse($student->isAdmin());
    }

    public function test_user_auth_identifier_remains_primary_key(): void
    {
        $user = new User();

        $this->assertSame('id', $user->getAuthIdentifierName());
    }
}
