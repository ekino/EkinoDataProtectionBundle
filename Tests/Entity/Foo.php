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
 * Class Foo.
 *
 * @AnonymizedEntity()
 * @author Benoit Mazière <benoit.maziere@ekino.com>
 */
class Foo implements ClassMetadataProviderInterface
{
    /**
     * @var string
     * @AnonymizedProperty(value="lorem")
     */
    private $bar;

    /**
     * @var string
     */
    private $baz;

    public static function getFieldMappings(): array
    {
        return ['bar' => ['fieldName' => 'bar'], 'baz' => ['fieldName' => 'baz']];
    }

    public static function getFieldNames(): array
    {
        return ['bar' => 'bar', 'baz' => 'baz'];
    }

    public static function getAssociationMappings(): array
    {
        return [];
    }
}
