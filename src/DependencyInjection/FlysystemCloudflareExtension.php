<?php

declare(strict_types=1);

namespace Softavis\FlysystemCloudflareBundle\DependencyInjection;

use League\Flysystem\Filesystem;
use Softavis\Flysystem\Cloudflare\Client;
use Softavis\Flysystem\Cloudflare\CloudflareAdapter;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class FlysystemCloudflareExtension extends Extension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container): void
    {
    }

    public function prepend(ContainerBuilder $container): void
    {
        $configs = $container->getExtensionConfig('flysystem');

        array_map(
            function(array $config) use ($container) {
                foreach ($config['storages'] as $storageName => $storageOptions) {
                    if ('cloudflare' === $storageOptions['adapter']) {
                        $options = $this->resolveOptions($storageOptions['options'] ?? []);

                        $this->buildStorageAdapterDefinition($container, $storageName, $options);
                    }
                }
            },
            $configs,
        );
    }

    private function resolveOptions(array $storageOptions): array
    {
        $resolver = new OptionsResolver();

        $resolver->setRequired('token');
        $resolver->setAllowedTypes('token', 'string');

        $resolver->setRequired('accountId');
        $resolver->setAllowedTypes('accountId', 'string');

        $resolver->setRequired('accountHash');
        $resolver->setAllowedTypes('accountHash', 'string');

        $resolver->setDefault('variantName', 'public');
        $resolver->setAllowedTypes('variantName', 'string');

        return $resolver->resolve($storageOptions);
    }

    private function buildStorageAdapterDefinition(
        ContainerBuilder $container,
        string $storageName,
        array $storageOptions,
    ): void
    {
        $client = new Definition();
        $client->setPublic(false);
        $client->setClass(Client::class);
        $client->setArguments([
            0 => new Reference('http_client'),
            1 => $storageOptions['accountId'],
            2 => $storageOptions['token'],
        ]);

        $definition = new Definition();
        $definition->setPublic(false);
        $definition->setClass(CloudflareAdapter::class);
        $definition->setArguments([
            0 => $client,
            1 => $storageOptions['accountHash'],
            2 => $storageOptions['variantName'],
        ]);

        $container->setDefinition('cloudflare', $definition);

        $definition = new Definition(Filesystem::class);
        $definition->setPublic(false);
        $definition->setArgument(0, new Reference('softavis_flysystem.adapter'));

        $definition->addTag('flysystem.storage', ['storage' => $storageName]);

        $container->setDefinition($storageName, $definition);
    }
}