<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller;

trait AuthSessionTrait
{
    protected function loginUser(): void
    {
        $this->session([
            'Config' => [
                'time' => 1742087458,
            ],
            'Auth' => [
                'User' => [
                    'email' => 'jacob@lbdscouts.org.uk',
                    'subject' => '712277bf-88bc-4ad5-a87d-f3b4fd0051d5',
                    'first_name' => 'Jacob',
                    'last_name' => 'Tyler',
                    'token' => 'fake.token',
                ],
                'expires_at' => 1742088064,
            ],
        ]);
    }

    protected function enableFormTokens(): void
    {
        $this->enableCsrfToken();
        $this->enableSecurityToken();
    }
}
