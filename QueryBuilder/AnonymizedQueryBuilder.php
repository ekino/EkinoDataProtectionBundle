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

namespace Ekino\DataProtectionBundle\QueryBuilder;

use Ekino\DataProtectionBundle\Annotations\AnonymizedProperty;
use Ekino\DataProtectionBundle\Meta\AnonymizedMetadata;

/**
 * Class AnonymizedQueryBuilder.
 * This class aims to build an anonymizing query based on an AnonymizedMetadata object.
 *
 * @author Benoit Mazière <benoit.maziere@ekino.com>
 */
final class AnonymizedQueryBuilder
{
    public function buildQuery(AnonymizedMetadata $anonymizedMetadata): string
    {
        $anonymizedEntity = $anonymizedMetadata->getAnonymizedEntity();

        if ($anonymizedEntity->isTruncateAction()) {
            $query = $this->buildTruncateQuery($anonymizedMetadata);
        } elseif ($anonymizedEntity->isAnonymizeAction()) {
            $query = $this->buildAnonymizeQuery($anonymizedMetadata);
        } else {
            throw new \LogicException(
                sprintf('"%s" action is not expected as this point to generate a valid query.',
                $anonymizedEntity->getAction()
            ));
        }

        return $this->suffixWithWhereClause($query, $anonymizedMetadata->getAnonymizedEntity()->getExceptWhereClause());
    }

    private function buildTruncateQuery(AnonymizedMetadata $anonymizedMetadata): string
    {
        $exceptWhereClause = $anonymizedMetadata->getAnonymizedEntity()->getExceptWhereClause();

        return !$exceptWhereClause ?
            sprintf('TRUNCATE TABLE %s', $anonymizedMetadata->getClassMetadata()->getTableName())
            : sprintf('DELETE FROM %s', $anonymizedMetadata->getClassMetadata()->getTableName())
        ;
    }

    private function buildAnonymizeQuery(AnonymizedMetadata $anonymizedMetadata): string
    {
        $setters              = [];
        $anonymizedProperties = $anonymizedMetadata->getAnonymizedProperties();
        
        foreach ($anonymizedProperties as $property => $anonymizedProperty) {
            $setters[] = $this->buildPropertySetterQueryPart($anonymizedProperty);
        }

        return sprintf('UPDATE %s SET %s',
            $anonymizedMetadata->getClassMetadata()->getTableName(), implode(', ', array_filter($setters)));
    }
    
    private function buildPropertySetterQueryPart(AnonymizedProperty $anonymizedProperty): string
    {
        if ($anonymizedProperty->isComposed()) {
            $composedFieldParts = $anonymizedProperty->explodeComposedFieldValue();

            return sprintf('%s = concat(concat("%s", %s), "%s")', $anonymizedProperty->getColumnName(),
                $composedFieldParts[1], $composedFieldParts[2],$composedFieldParts[3]);
        }

        if ($anonymizedProperty->isStatic() || $anonymizedProperty->isExpression()) {
            $propertyValue = $anonymizedProperty->isStatic() ?
                sprintf('"%s"', $anonymizedProperty->getValue()) : $anonymizedProperty->getValue();

            return sprintf('%s = %s', $anonymizedProperty->getColumnName(), $propertyValue);
        }

        throw new \LogicException(
            sprintf('"%s" type is not expected as this point to generate a valid query.',$anonymizedProperty->getType())
        );
    }

    private function suffixWithWhereClause(string $baseQuery, ?string $exceptWhereClause): string
    {
        return $exceptWhereClause ? sprintf('%s WHERE %s', $baseQuery, $exceptWhereClause) : $baseQuery;
    }
}
