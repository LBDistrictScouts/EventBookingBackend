<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Entity;

use App\Model\Entity\Entry;
use Cake\TestSuite\TestCase;

class EntryTest extends TestCase
{
    public function testSecurityCodeIsGeneratedWhenBlank(): void
    {
        $entry = new Entry(['security_code' => '']);

        $this->assertMatchesRegularExpression('/^[A-Z0-9]{5}$/', $entry->security_code);
    }

    public function testSecurityCodeIsPreservedWhenProvided(): void
    {
        $entry = new Entry(['security_code' => 'ABCDE']);

        $this->assertSame('ABCDE', $entry->security_code);
    }
}
