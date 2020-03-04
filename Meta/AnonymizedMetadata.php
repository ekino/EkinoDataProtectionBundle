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

use Doctrine\ORM\Mapping\ClassMetadata;
use Ekino\DataProtectionBundle\Annotations\AnonymizedEntity;
use Ekino\DataProtectionBundle\Annotations\AnonymizedProperty;

/**
 * Class AnonymizedMetadata.
 * Object containing all required metadatas (anonymize annotation & doctrine metadata) to build an anonymizing query.
 *
 * @author Benoit Mazière <benoit.maziere@ekino.com>
 */
final class AnonymizedMetadata
{
    private $classMetadata;

    private $anonymizedEntity;

    /**
     * @var AnonymizedProperty[]
     */
    private $anonymizedProperties = [];

    public function __construct(ClassMetadata $classMetadata, AnonymizedEntity $anonymizedEntity, iterable $anonymizedProperties)
    {
        $this->classMetadata        = $classMetadata;
        $this->anonymizedEntity     = $anonymizedEntity;
        $this->anonymizedProperties = $anonymizedProperties;
    }

    public function getClassMetadata(): ClassMetadata
    {
        return $this->classMetadata;
    }

    public function getAnonymizedEntity(): AnonymizedEntity
    {
        return $this->anonymizedEntity;
    }

    public function getAnonymizedProperties(): array
    {
        return $this->anonymizedProperties;
    }
}
