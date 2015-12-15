<?php

/**
 * Copyright 2015 SURFnet B.V.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace OpenConext\ProfileBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('open_conext_profile');

        $rootNode
            ->children()
                ->arrayNode('locales')
                    ->info('The available application locales')
                    ->isRequired()
                    ->prototype('scalar')
                        ->validate()
                            ->ifTrue(function ($locale) {
                                return !is_string($locale);
                            })
                            ->thenInvalid('Available application locales should be strings')
                        ->end()
                    ->end()
                ->end()
                ->scalarNode('default_locale')
                    ->info('The default application locale')
                    ->isRequired()
                    ->validate()
                        ->ifTrue(function ($locale) {
                            return !is_string($locale);
                        })
                        ->thenInvalid('Default application locale should be a string')
                    ->end()
                ->end()
                ->scalarNode('locale_cookie_domain')
                    ->info('The domain for which the locale cookie is set')
                    ->isRequired()
                    ->validate()
                        ->ifTrue(function ($domain) {
                            return !is_string($domain);
                        })
                        ->thenInvalid('Locale cookie domain should be a string')
                    ->end()
                ->end()
                ->scalarNode('locale_cookie_key')
                    ->info('The key for which the locale cookie value is set')
                    ->isRequired()
                        ->validate()
                            ->ifTrue(function ($key) {
                                return !is_string($key);
                            })
                            ->thenInvalid('Locale cookie key should be a string')
                        ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
