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

namespace Ekino\DataProtectionBundle\Tests\QueryBuilder;

use Ekino\DataProtectionBundle\QueryBuilder\AnonymizedQueryBuilder;
use Ekino\DataProtectionBundle\Tests\Entity\Foo;
use Ekino\DataProtectionBundle\Tests\Entity\ValidAnonymizedEntity;
use Ekino\DataProtectionBundle\Tests\Entity\ValidTruncateAnonymizedEntity;
use Ekino\DataProtectionBundle\Tests\Meta\AnonymizedMetadataProviderTrait;
use PHPUnit\Framework\TestCase;

/**
 * Class AnonymizedQueryBuilderTest.
 *
 * @author Benoit Mazière <benoit.maziere@ekino.com>
 */
class AnonymizedQueryBuilderTest extends TestCase
{
    use AnonymizedMetadataProviderTrait;

    /**
     * @var AnonymizedQueryBuilder
     */
    private $anonymizedQueryBuilder;

    protected function setUp(): void
    {
        $this->anonymizedQueryBuilder = new AnonymizedQueryBuilder();
    }

    /**
     * @dataProvider getAnonymizedMetadatas
     */
    public function testBuildQuery(string $entityName, string $expectedQuery): void
    {
        $this->assertSame($expectedQuery,
            $this->anonymizedQueryBuilder->buildQuery($this->getAnonymizedMetadata($entityName))
        );
    }

    public function getAnonymizedMetadatas(): \Generator
    {
        yield 'Foo' => [
            Foo::class,
            'UPDATE foo SET bar = "lorem"'
        ];
        yield 'ValidTruncateAnonymizedEntity' => [
            ValidTruncateAnonymizedEntity::class,
            'TRUNCATE TABLE validtruncateanonymizedentity'
        ];
        yield 'ValidAnonymizedEntity' => [
            ValidAnonymizedEntity::class,
            'UPDATE validanonymizedentity SET bar = "lorem", baz = concat(concat("lorem-", id), ""), foo = CONCAT(FLOOR(1 + (RAND() * 1000)), id) WHERE foo NOT LIKE \'%bar%\''
        ];
    }
}
