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
 * Class AnonymizedPropertyNotEmptyOnTruncateAnonymizedEntity.
 *
 * @AnonymizedEntity(action="truncate")
 * @author Benoit Mazière <benoit.maziere@ekino.com>
 */
class AnonymizedPropertiesAndTruncateAnonymizedEntityAction implements ClassMetadataProviderInterface
{
    /**
     * @var string
     * @AnonymizedProperty()
     */
    private $bar;

    public static function getFieldMappings(): array
    {
        return ['bar' => ['fieldName' => 'bar']];
    }

    public static function getFieldNames(): array
    {
        return ['bar' => 'bar'];
    }

    public static function getAssociationMappings(): array
    {
        return [];
    }
}
