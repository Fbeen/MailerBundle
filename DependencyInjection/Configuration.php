<?php

namespace Fbeen\MailerBundle\DependencyInjection;

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
        $rootNode = $treeBuilder->root('fbeen_mailer');

        $rootNode
            ->children()
                ->scalarNode('company_logo')->defaultValue(null)->end()
                ->scalarNode('company_name')->defaultValue('Visit website')->end()
                ->arrayNode('mailaddresses')
                    ->children()
                        ->scalarNode('noreply')->isRequired()->end()
                        ->scalarNode('general')->isRequired()->end()
                        ->arrayNode('admins')->isRequired()->requiresAtLeastOneElement()
                            ->prototype('scalar')->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
        
        return $treeBuilder;
    }
}
