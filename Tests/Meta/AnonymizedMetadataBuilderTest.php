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

use Doctrine\Common\Annotations\AnnotationException;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\ClassMetadataFactory;
use Ekino\DataProtectionBundle\Annotations\AnonymizedProperty;
use Ekino\DataProtectionBundle\Meta\AnonymizedMetadata;
use Ekino\DataProtectionBundle\Meta\AnonymizedMetadataBuilder;
use Ekino\DataProtectionBundle\Tests\Entity\AnonymizedPropertyWithoutAnonymizedEntity;
use Ekino\DataProtectionBundle\Tests\Entity\ClassMetadataProviderTrait;
use Ekino\DataProtectionBundle\Tests\Entity\Foo;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class AnonymizedMetadataBuilderTest.
 *
 * @author Benoit Mazière <benoit.maziere@ekino.com>
 */
class AnonymizedMetadataBuilderTest extends TestCase
{
    use ClassMetadataProviderTrait;

    /**
     * @var AnonymizedMetadataBuilder
     */
    private $anonymizedMetadataBuilder;

    /**
     * @var EntityManager|MockObject
     */
    private $entityManager;

    /**
     * @var AnnotationReader
     */
    private $annotationReader;

    /**
     * @var ClassMetadataFactory|MockObject
     */
    private $classMetadataFactory;

    protected function setUp(): void
    {
        $this->entityManager             = $this->createMock(EntityManager::class);
        $this->annotationReader          = new AnnotationReader();
        $this->classMetadataFactory      = $this->createMock(ClassMetadataFactory::class);
        $this->entityManager->expects($this->once())->method('getMetadataFactory')
            ->willReturn($this->classMetadataFactory);
        $this->anonymizedMetadataBuilder = new AnonymizedMetadataBuilder($this->entityManager, $this->annotationReader);
    }

    public function testBuildWithoutAnonymizedEntity(): void
    {
        $this->expectException(AnnotationException::class);
        $this->expectExceptionMessage(
            '[Creation Error] You tried to anonymize a property without specifying it at class level');

        $classMetadata = $this->getClassMetadata(AnonymizedPropertyWithoutAnonymizedEntity::class);
        $this->classMetadataFactory->expects($this->once())->method('getAllMetadata')->willReturn([$classMetadata]);

        $this->anonymizedMetadataBuilder->build()->current();
    }

    public function testBuild(): void
    {
        $classMetadata = $this->getClassMetadata(Foo::class);
        $this->classMetadataFactory->expects($this->once())->method('getAllMetadata')->willReturn([$classMetadata]);

        /** @var \Generator<AnonymizedMetadata> $anonymizedMetadatas */
        $anonymizedMetadatas = $this->anonymizedMetadataBuilder->build();
        foreach ($anonymizedMetadatas as $anonymizedMetadata) {
            $this->assertNotNull($anonymizedMetadata->getAnonymizedEntity());
            $this->assertInstanceOf(ClassMetadata::class, $anonymizedMetadata->getClassMetadata());

            /** @var AnonymizedProperty[] $anonymizedProperties */
            $anonymizedProperties = $anonymizedMetadata->getAnonymizedProperties();
            $this->assertCount(1, $anonymizedProperties);
            $anonymizedProperty = $anonymizedProperties[0];
            $this->assertSame('lorem', $anonymizedProperty->getValue());
            $this->assertSame(AnonymizedProperty::TYPE_STATIC, $anonymizedProperty->getType());
            $this->assertSame('bar', $anonymizedProperty->getColumnName());
            $this->assertSame('bar', $anonymizedProperty->getFieldName());
        }
    }
}
