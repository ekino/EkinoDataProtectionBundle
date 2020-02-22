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

namespace Ekino\DataProtectionBundle\Tests\Entity;

use Ekino\DataProtectionBundle\Annotations\AnonymizedEntity;
use Ekino\DataProtectionBundle\Annotations\AnonymizedProperty;

/**
 * Class ComposedAnonymizedPropertyWithNotUniqueField.
 *
 * @AnonymizedEntity()
 * @author Benoit Mazière <benoit.maziere@ekino.com>
 */
class ComposedAnonymizedPropertyWithNotUniqueField implements ClassMetadataProviderInterface
{
    /**
     * @var string
     * @AnonymizedProperty(type="composed", value="test-<foo>")
     */
    private $bar;

    /**
     * @var string
     */
    private $foo;

    public static function getFieldMappings(): array
    {
        return [
            'bar' => ['fieldName' => 'bar', 'unique' => true],
            'foo' => ['fieldName' => 'foo', 'unique' => false]];
    }

    public static function getFieldNames(): array
    {
        return ['bar' => 'bar', 'foo' => 'foo'];
    }

    public static function getAssociationMappings(): array
    {
        return [];
    }
}
