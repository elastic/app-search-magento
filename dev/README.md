# Installing your dev environment.

This documentation is intended to help you to install your environment in the following cases:
* You want to contribute to App Search Magento module as a developer.
* You need a full featured Magento environment with App Search enabled for testing / QA purpose.

## Requirements:

### Docker

The development environment is dockerized, you will need Docker to be installed on your dev environment.

## Building the stack:

```bash
docker-compose build --build-arg public_key="<public_key>" --build-arg private_key="<private_key>"
```

```bash
docker-compose up -d
````

```bash
docker-compose exec magento sudo -u www-data -E /bin/bash
```

## Configuring && installing App Search module:

```bash
composer require "swiftype/swiftype-app-search-magento"
```

## Installing Magento:



## Useful commands:
