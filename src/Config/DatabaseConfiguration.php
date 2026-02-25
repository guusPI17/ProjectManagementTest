<?php

declare(strict_types=1);

namespace App\Config;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class DatabaseConfiguration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('database');

        $treeBuilder->getRootNode()
            ->children()
                ->scalarNode('driver')->defaultValue('pgsql')
                    ->validate()
                        ->ifNotInArray(['pgsql', 'mysql'])
                        ->thenInvalid('Ошибочный драйвер базы данных "%s". Допустимые: pgsql, mysql.')
                    ->end()
                ->end()
                ->scalarNode('host')->isRequired()->cannotBeEmpty()->end()
                ->integerNode('port')->defaultValue(5432)->end()
                ->scalarNode('name')->isRequired()->cannotBeEmpty()->end()
                ->scalarNode('user')->isRequired()->end()
                ->scalarNode('password')->defaultValue('')->end()
            ->end();

        return $treeBuilder;
    }
}
