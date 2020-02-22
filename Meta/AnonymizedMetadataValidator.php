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
use Doctrine\ORM\Mapping\ClassMetadata;
use Ekino\DataProtectionBundle\Annotations\AnonymizedEntity;
use Ekino\DataProtectionBundle\Annotations\AnonymizedProperty;

/**
 * Class AnonymizedMetadataValidator.
 * This class aims to validate your anonymize annotations regarding your doctrine metadatas to give the best chance to
 * build valid anonymizing sql queries.
 *
 * @author Benoit Mazière <benoit.maziere@ekino.com>
 */
final class AnonymizedMetadataValidator
{
    public function validate(AnonymizedMetadata $anonymizedMetadata): void
    {
        $this->anonymizedPropertiesMustBeEmptyIfAnonymizedEntityActionIsTruncate($anonymizedMetadata);

        $anonymizedProperties = $anonymizedMetadata->getAnonymizedProperties();
        $classMetadata        = $anonymizedMetadata->getClassMetadata();

        foreach ($anonymizedProperties as $anonymizedProperty) {
            $this->propertyMustBeNullableIfStaticValueIsNull($anonymizedProperty, $classMetadata);
            $this->propertyMustExistsInComposedExpression($anonymizedProperty, $classMetadata);
            $this->composedFieldForUniquePropertyMustBeUnique($anonymizedProperty, $classMetadata);
            $this->anonymizedPropertyMustBeComposedIfFieldHasUniqueIndex($anonymizedProperty, $classMetadata);
            $this->associationPropertyMustNotHaveAnonymizedProperty($anonymizedProperty, $classMetadata);
        }
    }

    private function anonymizedPropertiesMustBeEmptyIfAnonymizedEntityActionIsTruncate(
        AnonymizedMetadata $anonymizedMetadata): void
    {
        $anonymizedEntity = $anonymizedMetadata->getAnonymizedEntity();

        if ($anonymizedEntity->isTruncateAction()
            && !empty($anonymizedMetadata->getAnonymizedProperties())) {
            throw AnnotationException::creationError(
                sprintf('If %s action is set at class level, it can\'t have property annotation in %s',
                    AnonymizedEntity::ACTION_TRUNCATE, $anonymizedMetadata->getClassMetadata()->getName()));
        }
    }

    private function anonymizedPropertyMustBeComposedIfFieldHasUniqueIndex(
        AnonymizedProperty $anonymizedProperty,
        ClassMetadata $classMetadata): void
    {
        if ($classMetadata->isUniqueField($anonymizedProperty->getFieldName()) && !$anonymizedProperty->isComposed()) {
            throw AnnotationException::creationError(
                sprintf('If property is unique (%s), AnonymzedProperty must be of type %s in %s',
                    $anonymizedProperty->getFieldName(), AnonymizedProperty::TYPE_COMPOSED, $classMetadata->getName()));
        }
    }

    private function propertyMustExistsInComposedExpression(
        AnonymizedProperty $anonymizedProperty,
        ClassMetadata $classMetadata): void
    {
        if (!$anonymizedProperty->isComposed()) {
            return;
        }

        $value         = $anonymizedProperty->getValue();
        $composedField = $anonymizedProperty->extractComposedFieldFromValue();

        if (empty($composedField)) {
            throw AnnotationException::creationError(
                sprintf('No composed field specified in composed expression of %s property in %s',
                    $anonymizedProperty->getFieldName(), $classMetadata->getName()));
        }

        if (!\in_array($composedField, $classMetadata->getFieldNames())) {
            throw AnnotationException::creationError(
                sprintf('Property %s specified in composed expression of %s does not exists in %s',
                    $composedField, $anonymizedProperty->getFieldName(), $classMetadata->getName()));
        }
    }

    private function composedFieldForUniquePropertyMustBeUnique(
        AnonymizedProperty $anonymizedProperty,
        ClassMetadata $classMetadata): void
    {
        if (!$anonymizedProperty->isComposed()) {
            return;
        }

        $composedField = $anonymizedProperty->extractComposedFieldFromValue();

        if ($classMetadata->isUniqueField($anonymizedProperty->getFieldName())
            && !$classMetadata->isUniqueField($composedField)) {
            throw AnnotationException::creationError(
                sprintf('If property is unique (%s), composed field %s must be unique to avoid duplicate potential value in %s',
                    $anonymizedProperty->getFieldName(), $composedField, $classMetadata->getName()));
        }
    }

    private function propertyMustBeNullableIfStaticValueIsNull(
        AnonymizedProperty $anonymizedProperty,
        ClassMetadata $classMetadata): void
    {
        if ($anonymizedProperty->isStatic()
            && \is_null($anonymizedProperty->getValue())
            && !$classMetadata->isNullable($anonymizedProperty->getFieldName())) {
            throw AnnotationException::creationError(
                sprintf('Property %s is supposed to be anonymized to null value but is not nullable in %s',
                    $anonymizedProperty->getFieldName(), $classMetadata->getName()));
        }
    }

    private function associationPropertyMustNotHaveAnonymizedProperty(
        AnonymizedProperty $anonymizedProperty,
        ClassMetadata $classMetadata): void
    {
        if (\in_array($anonymizedProperty->getFieldName(), $classMetadata->getAssociationNames())) {
            throw AnnotationException::creationError(
                sprintf('Anonymization of associations (%s) is not supported in %s',
                    $anonymizedProperty->getFieldName(), $classMetadata->getName()));
        }
    }
}
