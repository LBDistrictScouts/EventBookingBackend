<?php
declare(strict_types=1);

namespace App\Test\TestCase\Utility;

use App\Utility\CodeUtility;
use Cake\TestSuite\TestCase;

class CodeUtilityTest extends TestCase
{
    public function testGenerateCodeUsesRequestedLength(): void
    {
        $code = CodeUtility::generateCode(8);

        $this->assertSame(8, strlen($code));
        $this->assertMatchesRegularExpression('/^[A-Z0-9]{8}$/', $code);
    }

    public function testGenerateCodeDefaultsToFiveCharacters(): void
    {
        $code = CodeUtility::generateCode();

        $this->assertSame(5, strlen($code));
        $this->assertMatchesRegularExpression('/^[A-Z0-9]{5}$/', $code);
    }
}
