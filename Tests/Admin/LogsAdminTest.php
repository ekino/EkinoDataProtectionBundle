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

namespace Ekino\DataProtectionBundle\Tests\Admin;

use Ekino\DataProtectionBundle\Admin\LogsAdmin;
use Ekino\DataProtectionBundle\Controller\LogsAdminController;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use Sonata\AdminBundle\Route\RouteCollection;

/**
 * Class LogsAdminTest.
 *
 * @author Benoit Mazière <benoit.maziere@ekino.com>
 */
class LogsAdminTest extends TestCase
{
    /**
     * @var LogsAdmin
     */
    private $admin;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        $this->admin = new LogsAdmin('', '', LogsAdminController::class);
    }

    /**
     * Test configureRoutes method.
     */
    public function testConfigureRoutes(): void
    {
        $routeCollection = $this->createMock(RouteCollection::class);
        $routeCollection->expects($this->once())
            ->method('add')
            ->with('decrypt_encrypt', 'decrypt-encrypt', [], [], [], '', [], ['GET', 'POST']);

        $exec = new ReflectionMethod($this->admin, 'configureRoutes');
        $exec->setAccessible(true);
        $exec->invoke($this->admin, $routeCollection);
    }
}
