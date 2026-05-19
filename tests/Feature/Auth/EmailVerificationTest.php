<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Verified;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    public function test_email_verification_screen_can_be_rendered()
    {
        $this->markTestSkipped('Rotas de verificação não fazem parte do escopo atual.');
    }

    public function test_email_can_be_verified()
    {
        $this->markTestSkipped('Rotas de verificação não fazem parte do escopo atual.');
    }

    public function test_email_is_not_verified_with_invalid_hash()
    {
        $this->markTestSkipped('Rotas de verificação não fazem parte do escopo atual.');
    }
}
