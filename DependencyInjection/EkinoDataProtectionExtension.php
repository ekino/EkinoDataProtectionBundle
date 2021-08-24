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

namespace Ekino\DataProtectionBundle\DependencyInjection;

use Sonata\AdminBundle\SonataAdminBundle;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @link http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
class EkinoDataProtectionExtension extends Extension
{
    /**
     * {@inheritdoc}
     *
     * @param array<non-empty-array> $configs
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config        = $this->processConfiguration($configuration, $configs);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        $this->configureEncryptor($config['encryptor'], $container);
        $this->configureEncryptCommand($config['encryptor'], $container);

        if (!$config['encrypt_logs']) {
            $container->removeDefinition('ekino_data_protection.monolog.processor.gdpr');
        }

        $this->configureSonataAdmin($config['use_sonata_admin'], $container, $loader);
    }

    /**
     * @param array<array-key,string> $config
     * @param ContainerBuilder        $container
     */
    private function configureEncryptor(array $config, ContainerBuilder $container): void
    {
        $container
            ->findDefinition('ekino_data_protection.encryptor')
            ->replaceArgument(0, $config['method'])
            ->replaceArgument(1, $config['secret']);
    }

    /**
     * @param array<array-key,string> $config
     * @param ContainerBuilder        $container
     */
    private function configureEncryptCommand(array $config, ContainerBuilder $container): void
    {
        $container
            ->findDefinition('ekino_data_protection.command.encryptor')
            ->replaceArgument(0, $config['method'])
            ->replaceArgument(1, $config['secret']);
    }

    /**
     * @param bool             $useSonataAdmin
     * @param ContainerBuilder $container
     * @param XmlFileLoader    $loader
     *
     * @throws \LogicException
     */
    private function configureSonataAdmin(bool $useSonataAdmin, ContainerBuilder $container, XmlFileLoader $loader): void
    {
        if ($useSonataAdmin) {
            if (!class_exists(SonataAdminBundle::class)) {
                throw new \LogicException('Please install sonata-project/admin-bundle or turn off use_sonata_admin');
            }

            $loader->load('sonata_admin.xml');
        }
    }
}
