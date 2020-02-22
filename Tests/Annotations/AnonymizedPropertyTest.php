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

use Ekino\DataProtectionBundle\Annotations\AnonymizedProperty;
use PHPUnit\Framework\TestCase;

/**
 * Class AnonymizedPropertyTest.
 *
 * @author Benoit Mazière <benoit.maziere@ekino.com>
 */
class AnonymizedPropertyTest extends TestCase
{
    public function testAnonymizedEntityWithInvalidProperty(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Option "foo" does not exist');

        new AnonymizedProperty([
            'foo' => 'bar',
        ]);
    }

    public function testAnonymizedEntityWithInvalidType(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Type "foo" is not allowed. Allowed types are: static, composed, expression');

        new AnonymizedProperty([
            'type' => 'foo',
        ]);
    }

    public function testWithValidConfiguration(): void
    {
        $anonymizedProperty = new AnonymizedProperty([
            'type'  => AnonymizedProperty::TYPE_COMPOSED,
            'value' => 'test-<id>',
        ]);

        $this->assertTrue($anonymizedProperty->isComposed());
        $this->assertFalse($anonymizedProperty->isStatic());
        $this->assertFalse($anonymizedProperty->isExpression());
        $this->assertSame('id', $anonymizedProperty->extractComposedFieldFromValue());
        $this->assertSame(['test-<id>', 'test-', 'id', ''], $anonymizedProperty->explodeComposedFieldValue());
    }
}
