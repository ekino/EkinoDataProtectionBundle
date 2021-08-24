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

namespace Ekino\DataProtectionBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Route\RouteCollection;

/**
 * Class LogsAdmin.
 *
 * @author Benoit Mazière <benoit.maziere@ekino.com>
 */
class LogsAdmin extends AbstractAdmin
{
    /**
     * @var string
     */
    protected $baseRoutePattern = '/app/logs';

    /**
     * @var string
     */
    protected $baseRouteName = 'admin_app_logs';

    /**
     * {@inheritdoc}
     */
    protected function configureRoutes(RouteCollection $collection): void
    {
        $collection->add('decrypt_encrypt', 'decrypt-encrypt', [], [], [], '', [], ['GET', 'POST']);
    }
}
