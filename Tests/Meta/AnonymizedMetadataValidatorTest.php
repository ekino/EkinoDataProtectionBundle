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
use Ekino\DataProtectionBundle\Meta\AnonymizedMetadataValidator;
use Ekino\DataProtectionBundle\Tests\Entity\AnonymizedPropertiesAndTruncateAnonymizedEntityAction;
use Ekino\DataProtectionBundle\Tests\Entity\AnonymizedPropertyNullOnNotNullableProperty;
use Ekino\DataProtectionBundle\Tests\Entity\AnonymizedPropertyOnAssociationField;
use Ekino\DataProtectionBundle\Tests\Entity\AnonymizedPropertyWithoutAnonymizedEntity;
use Ekino\DataProtectionBundle\Tests\Entity\ComposedAnonymizedPropertyWithNotUniqueField;
use Ekino\DataProtectionBundle\Tests\Entity\ComposedAnonymizedPropertyWithoutComposedField;
use Ekino\DataProtectionBundle\Tests\Entity\ComposedAnonymizedPropertyWithUnknownField;
use Ekino\DataProtectionBundle\Tests\Entity\UniqueFieldWithoutComposedAnonymizedProperty;
use PHPUnit\Framework\TestCase;

/**
 * Class AnonymizedMetadataValidatorTest.
 *
 * @author Benoit Mazière <benoit.maziere@ekino.com>
 */
class AnonymizedMetadataValidatorTest extends TestCase
{
    use AnonymizedMetadataProviderTrait;

    /**
     * @var AnonymizedMetadataValidator
     */
    private $anonymizedMetadataValidator;

    protected function setUp(): void
    {
        $this->anonymizedMetadataValidator = new AnonymizedMetadataValidator();
    }

    /**
     * @dataProvider getInvalidConfigirations
     */
    public function testWithInvalidConfiguration(string $exceptionClass, string $exceptionMessage, string $entityName): void
    {
        $this->expectException($exceptionClass);
        $this->expectExceptionMessage($exceptionMessage);

        $this->anonymizedMetadataValidator->validate($this->getAnonymizedMetadata($entityName));
    }

    public function getInvalidConfigirations(): \Generator
    {
        yield 'testValidateWithAnonymizedPropertiesAndMissingAnonymizedEntity' => [
            AnnotationException::class,
            '[Creation Error] You tried to anonymize a property without specifying it at class level',
            AnonymizedPropertyWithoutAnonymizedEntity::class
        ];
         yield 'testValidateWithAnonymizedPropertiesAndTruncateAnonymizedEntityAction' => [
             AnnotationException::class,
             sprintf('[Creation Error] If truncate action is set at class level, it can\'t have property annotation in %s',
                 AnonymizedPropertiesAndTruncateAnonymizedEntityAction::class),
             AnonymizedPropertiesAndTruncateAnonymizedEntityAction::class
         ];
         yield 'testValidateWithAnonymizedPropertyOnAssociationField' => [
             AnnotationException::class,
             sprintf('[Creation Error] Anonymization of associations (bar) is not supported in %s',
                 AnonymizedPropertyOnAssociationField::class),
             AnonymizedPropertyOnAssociationField::class
         ];
         yield 'testValidatePropertyIsNullableOnNullValue' => [
             AnnotationException::class,
             sprintf('[Creation Error] Property bar is supposed to be anonymized to null value but is not nullable in %s',
                 AnonymizedPropertyNullOnNotNullableProperty::class),
             AnonymizedPropertyNullOnNotNullableProperty::class
         ];
         yield 'testValidateComposedAnonymizedPropertyWithoutComposedField' => [
             AnnotationException::class,
             sprintf('[Creation Error] No composed field specified in composed expression of bar property in %s',
                 ComposedAnonymizedPropertyWithoutComposedField::class),
             ComposedAnonymizedPropertyWithoutComposedField::class
         ];
         yield 'testValidateComposedAnonymizedPropertyWithUnknownField' => [
             AnnotationException::class,
             sprintf('[Creation Error] Property foo specified in composed expression of bar does not exists in %s',
                 ComposedAnonymizedPropertyWithUnknownField::class),
             ComposedAnonymizedPropertyWithUnknownField::class
         ];
         yield 'testValidateComposedAnonymizedPropertyWithNotUniqueField' => [
             AnnotationException::class,
             sprintf('[Creation Error] If property is unique (bar), composed field foo must be unique to avoid duplicate potential value in %s',
                 ComposedAnonymizedPropertyWithNotUniqueField::class),
             ComposedAnonymizedPropertyWithNotUniqueField::class
         ];
         yield 'testValidateUniqueFieldWithoutComposedAnonymizedProperty' => [
             AnnotationException::class,
             sprintf('[Creation Error] If property is unique (bar), AnonymzedProperty must be of type composed in %s',
                 UniqueFieldWithoutComposedAnonymizedProperty::class),
             UniqueFieldWithoutComposedAnonymizedProperty::class
         ];
    }
}
