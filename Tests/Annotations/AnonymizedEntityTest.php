<?php

declare(strict_types=1);

/*
 * This file is part of the ekino/data-protection-bundle project.
 *
 * (c) Ekino
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ekino\DataProtectionBundle\Tests\Annotations;

use Ekino\DataProtectionBundle\Annotations\AnonymizedEntity;
use PHPUnit\Framework\TestCase;

/**
 * Class AnonymizedEntityTest.
 *
 * @author Benoit Mazière <benoit.maziere@ekino.com>
 */
class AnonymizedEntityTest extends TestCase
{
    public function testAnonymizedEntityWithInvalidProperty(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Option "foo" does not exist');

        new AnonymizedEntity([
            'foo' => 'bar',
        ]);
    }

    public function testAnonymizedEntityWithInvalidAction(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Action "foo" is not allowed. Allowed actions are: anonymize, truncate');

        new AnonymizedEntity([
            'action' => 'foo',
        ]);
    }

    public function testWithValidConfiguration(): void
    {
        $anonymizedEntity = new AnonymizedEntity([
            'action'            => AnonymizedEntity::ACTION_TRUNCATE,
            'exceptWhereClause' => 'roles NOT LIKE %foo%',
        ]);

        $this->assertTrue($anonymizedEntity->isTruncateAction());
        $this->assertFalse($anonymizedEntity->isAnonymizeAction());
        $this->assertSame($anonymizedEntity->getExceptWhereClause(), 'roles NOT LIKE %foo%');
        $this->assertSame($anonymizedEntity->getAction(), AnonymizedEntity::ACTION_TRUNCATE);
    }
}
