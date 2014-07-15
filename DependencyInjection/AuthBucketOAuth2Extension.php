<?php

/**
 * This file is part of the authbucket/oauth2-bundle package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AuthBucket\Bundle\OAuth2Bundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class AuthBucketOAuth2Extension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $config = $this->processConfiguration(
            new Configuration(),
            $configs
        );

        $loader = new Loader\YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config')
        );

        $loader->load('services.yml');

        if (!empty($config['driver'])) {
            if (in_array($config['driver'], array('orm'))) {
                $loader->load(sprintf('%s.yml', $config['driver']));
            }
            unset($config['driver']);
        }

        if (!empty($config['user_provider'])) {
            $container->getDefinition('authbucket_oauth2.token_controller')
                ->replaceArgument(6, new Reference($config['user_provider']));
            unset($config['user_provider']);
        }

        foreach (array_filter($config) as $key => $value) {
            $container->setParameter('authbucket_oauth2.' . $key, $value);
        }
    }

    public function getAlias()
    {
        return 'authbucket_oauth2';
    }
}
