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
 * Class ValidAnonymizedEntity.
 *
 * @AnonymizedEntity(exceptWhereClause="foo NOT LIKE '%bar%'")
 * @author Benoit Mazière <benoit.maziere@ekino.com>
 */
class ValidAnonymizedEntity implements ClassMetadataProviderInterface
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     * @AnonymizedProperty(type="static", value="lorem")
     */
    private $bar;

    /**
     * @var string
     * @AnonymizedProperty(type="composed", value="lorem-<id>")
     */
    private $baz;

    /**
     * @var string
     * @AnonymizedProperty(type="expression", value="CONCAT(FLOOR(1 + (RAND() * 1000)), id)")
     */
    public $foo;

    public static function getFieldMappings(): array
    {
        return [
            'id'  => ['fieldName' => 'id', 'unique' => true],
            'bar' => ['fieldName' => 'bar'],
            'baz' => ['fieldName' => 'baz', 'unique' => true],
            'foo' => ['fieldName' => 'foo'],
        ];
    }

    public static function getFieldNames(): array
    {
        return ['id' => 'id', 'bar' => 'bar', 'baz' => 'baz', 'foo' => 'foo'];
    }

    public static function getAssociationMappings(): array
    {
        return [];
    }
}
