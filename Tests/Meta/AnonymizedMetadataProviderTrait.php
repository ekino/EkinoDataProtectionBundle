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

namespace Ekino\DataProtectionBundle\Tests\Meta;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadataFactory;
use Ekino\DataProtectionBundle\Meta\AnonymizedMetadata;
use Ekino\DataProtectionBundle\Meta\AnonymizedMetadataBuilder;
use Ekino\DataProtectionBundle\Tests\Entity\ClassMetadataProviderTrait;

/**
 * Trait AnonymizedMetadataProviderTrait.
 * This trait is an helper to build AnonymizedMetadata object from a test entity.
 *
 * @author Benoit Mazière <benoit.maziere@ekino.com>
 */
trait AnonymizedMetadataProviderTrait
{
    use ClassMetadataProviderTrait;

    private function getAnonymizedMetadata(string $className): AnonymizedMetadata
    {
        $classMetadata        = $this->getClassMetadata($className);
        $entityManager        = $this->createMock(EntityManager::class);
        $annotationReader     = new AnnotationReader();
        $classMetadataFactory = $this->createMock(ClassMetadataFactory::class);
        $classMetadataFactory->expects($this->once())->method('getAllMetadata')
            ->willReturn([$classMetadata]);
        $entityManager->expects($this->once())->method('getMetadataFactory')
            ->willReturn($classMetadataFactory);
        $anonymizedMetadataBuilder = new AnonymizedMetadataBuilder($entityManager, $annotationReader);

        $anonymizedMetadatas = $anonymizedMetadataBuilder->build();

        return $anonymizedMetadatas->current();
    }
}
