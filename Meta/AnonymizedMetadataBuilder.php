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

namespace Ekino\DataProtectionBundle\Meta;

use Doctrine\Common\Annotations\AnnotationException;
use Doctrine\Common\Annotations\Reader;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Ekino\DataProtectionBundle\Annotations\AnonymizedEntity;
use Ekino\DataProtectionBundle\Annotations\AnonymizedProperty;

/**
 * Class AnonymizedMetadataBuilder.
 * This class aims to build an AnonymizedMetadata object based on anonymize annotations defined on your entities.
 *
 * @author Benoit Mazière <benoit.maziere@ekino.com>
 */
final class AnonymizedMetadataBuilder
{
    private $entityManager;

    protected $annotationReader;

    public function __construct(EntityManagerInterface $entityManager, Reader $annotationReader)
    {
        $this->entityManager    = $entityManager;
        $this->annotationReader = $annotationReader;
    }

    /**
     * return \Generator<AnonymizedMetadata>
     */
    public function build(): \Generator
    {
        $anonymizedMetadatas = [];
        /** @var ClassMetadata[] $classMetadatas */
        $classMetadatas = $this->entityManager->getMetadataFactory()->getAllMetadata();

        foreach ($classMetadatas as $classMetadata) {
            $anonymizedEntity     = $this->buildAnonymizedEntityAnnotations($classMetadata);
            $anonymizedProperties = $this->buildAnonymizedPropertiesAnnotations($classMetadata);

            if (!$anonymizedEntity && !empty($anonymizedProperties)) {
                throw AnnotationException::creationError(
                    sprintf('You tried to anonymize a property without specifying it at class level in %s. 
                        You should add @AnonymizedEntity() in class phpdoc', $classMetadata->getName()));
            }

            if ($anonymizedEntity) {
                yield new AnonymizedMetadata($classMetadata, $anonymizedEntity, $anonymizedProperties);
            }
        }
    }

    private function buildAnonymizedPropertiesAnnotations(ClassMetadata $classMetadata): array
    {
        $anonymizedProperties = [];
        $properties           = $classMetadata->getFieldNames();

        foreach ($properties as $property) {
            /** @var AnonymizedProperty|null $anonymizedProperty */
            $anonymizedProperty = $this->annotationReader->getPropertyAnnotation(
                new \ReflectionProperty($classMetadata->getName(), $property),
                AnonymizedProperty::class
            );

            if (\is_null($anonymizedProperty)) {
                continue;
            }

            $anonymizedProperty->setFieldName($property)->setColumnName($classMetadata->getColumnName($property));
            $anonymizedProperties[] = $anonymizedProperty;
        }

        return $anonymizedProperties;
    }

    private function buildAnonymizedEntityAnnotations(ClassMetadata $classMetadata): ?AnonymizedEntity
    {
        /** @var AnonymizedEntity|null $anonymizedEntity */
        $anonymizedEntity = $this->annotationReader->getClassAnnotation(
            new \ReflectionClass($classMetadata->getName()), AnonymizedEntity::class
        );

        return $anonymizedEntity;
    }
}
