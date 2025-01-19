<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use Cake\ORM\Locator\LocatorAwareTrait;
use Cake\ORM\Table;
use Cake\TestSuite\TestCase;

trait TableTestTrait
{
    use LocatorAwareTrait;

    public function validatorTest(TestCase $context, Table $table, array $goodData, array $validation): void
    {
        $newGroup = $table->newEntity($goodData);
        $defaultValidator = $table->getValidator('default');
        $context->assertEmpty($defaultValidator->validate($newGroup->toArray()));

        $result = $table->save($newGroup);
        $context->assertEmpty($result->getErrors());

        /**
         * Tests for RequirePresence
         */
        foreach ($validation['require'] as $key) {
            $badData = $goodData;
            unset($badData[$key]);

            $result = $defaultValidator->validate($badData);
            $context->assertNotEmpty($result);
        }

        /**
         * Tests for NotEmptyString
         */
        foreach ($validation['notEmpty'] as $key) {
            $badData = $goodData;
            $badData[$key] = '';

            $result = $defaultValidator->validate($badData);
            $context->assertNotEmpty($result);
        }
    }
}
