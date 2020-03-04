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

/**
 * Interface ClassMetadataProviderInterface.
 * Test entities for anonymizing related tests should implement this interface to easily fake doctrine metadata.
 *
 * @author Benoit Mazière <benoit.maziere@ekino.com>
 */
interface ClassMetadataProviderInterface
{
    public static function getFieldMappings(): array;

    public static function getFieldNames(): array;

    public static function getAssociationMappings(): array;
}
