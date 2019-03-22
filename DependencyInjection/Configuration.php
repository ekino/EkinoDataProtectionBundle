<?php

/*
 * This file is part of the ekino/data-protection-bundle project.
 *
 * (c) Ekino
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ekino\DataProtectionBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode    = $treeBuilder->root('ekino_data_protection');

        $rootNode
            ->children()
                ->arrayNode('encryptor')
                    ->isRequired()
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('method')->defaultValue('aes-256-cbc')->end()
                        ->scalarNode('secret')->isRequired()->cannotBeEmpty()->end()
                    ->end()
                ->end()
                ->booleanNode('encrypt_logs')->defaultTrue()->end()
                ->booleanNode('use_sonata_admin')->defaultFalse()->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
