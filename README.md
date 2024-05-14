# flysystem-cloudflare-bundle

flysystem-cloudflare-bundle is a Symfony bundle integrating the [Flysystem](https://flysystem.thephpleague.com)
and [Cloudflare Images](https://www.cloudflare.com/developer-platform/cloudflare-images/) API into Symfony applications.

## Installation

flysystem-cloudflare-bundle requires PHP 7.2+ and Symfony 5.4+.

You can install the bundle using Symfony Flex:

```
composer require softavis/flysystem-cloudflare-bundle
```

## Basic usage

Configuration are already configured with league/flysystem-bundle, just change adapter to 'cloudflare' where you need it

```yaml
# config/packages/flysystem.yaml

flysystem:
  storages:
    default.storage:
      adapter: 'cloudflare'
      options:
        token: <your-cloudflare-access-token>
        accountId: <your-cloudflare-account-id>
        accountHash: <your-cloudflare-account-hash>
        variantName: public # Variant name use it by default
```

## Security Issues
