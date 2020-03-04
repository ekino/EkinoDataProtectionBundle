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

use Doctrine\ORM\Mapping\ClassMetadata;

/**
 * Trait ClassMetadataProviderTrait.
 * This trait is an helper to build fake doctrine metadata of a test entity.
 *
 * @author Benoit Mazière <benoit.maziere@ekino.com>
 */
trait ClassMetadataProviderTrait
{
    private function getClassMetadata(string $className): ClassMetadata
    {
        if (!is_a($className, ClassMetadataProviderInterface::class, true)) {
            throw new \InvalidArgumentException(sprintf('Class %s should be an instance of %s', $className,
                ClassMetadataProviderInterface::class));
        }

        $classMetadata                      = new ClassMetadata($className);
        $classMetadata->fieldMappings       = $className::getFieldMappings();
        $classMetadata->fieldNames          = $className::getFieldNames();
        $classMetadata->associationMappings = $className::getAssociationMappings();
        $classMetadata->table               = ['name' => strtolower((new \ReflectionClass($className))->getShortName())];

        return $classMetadata;
    }
}
