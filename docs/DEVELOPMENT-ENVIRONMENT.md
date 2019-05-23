# Installing your dev environment.

This documentation is intended to help you install your environment in the following cases:
* You want to contribute to App Search Magento module as a developer.
* You need a full featured Magento environment with App Search enabled for testing / QA purpose.

## Requirements:

### Docker:

The development environment is dockerized, you will need Docker to be installed on your dev environment.

### Magento Developer Account:

In order to download Magento, an Magento account is required. If you do not have one already, you can create it at: https://account.magento.com/customer/account/create/

Once your account is created AND VALIDATED (check your mail inbox to get the validation link), you should be able to access your credentials management page at the following address: https://marketplace.magento.com/customer/accessKeys/

Create an new access key and note both public and private key (you will need it while building the Magento docker container later).

### App Search Account:

You will need an App Search account. If you do not have one already, you can create it at: https://app.swiftype.com/signup

Once logged, you can find your App Search credentials at: https://app.swiftype.com/as/credentials

To install the dev environment you will need to configure both the API Endpoint a private API key and a search API key.

## Stack content:

The stack contains:
* Preconfigured component to store data (MySQL and Redis)
* A custom Apache PHP image containing all the Magento code (+ sample catalog data)

## Configure ENV file

Your App Search credentials are stored into the `dev/magento.env` file.

When building or launching the stack for the first time, you will need to create a `dev/magento.env` file. We recommend you copy `dev/magento.env.sample` and update the file with your App Search credentials before launching the stack.

**Note:** Every time you change something in the `dev/magento.env` file, the containers will be recreated if running `docker-compose up` and you will need to reinstall Magento.


## Building the stack:

Before being able to use the stack you will need to build the custom image:

```bash
docker-compose build --build-arg public_key="<public_key>" --build-arg private_key="<private_key>"
```

**Note :**
* Replace both `<public_key>` and `<private_key` by your credentials created in your Magento account
* Additional build args can be used to change the Magento version or to disable sample data install

Because there are a lot of dependencies to install the first run may takes several minutes. Consecutive runs will be faster.

## Running the stack

You can launch the stack by running:

```bash
docker-compose up -d
````

To invoke a bash session into the Magento container, you should use:

```bash
docker-compose exec magento sudo -u www-data -E /bin/bash
```

**Note:** We are using the `www-data` user in order to preserve access rights when using Magento command. You should never use another account.

## Installing App Search

We need to install the App Search module before we install Magento. Deploy the App Search module by issuing the following command (in a bash session in the container):

```bash
composer require "elastic/elastic-app-search-magento"
```

**Note:** The App Search module will be symlinked from your host machine. So any change you will do will be replicated in your Magento instance.

## Installing Magento

To install your Magento instance, login to the container and use:

```bash
bin/magento setup:install
```

The MySQL/Redis databases come preconfigured with the devbox, so you do not need to worry about it.

Once the setup is finished, you should be able to access to Magento from your browser at http://localhost:8888 (http://localhost:8888/admin for the admin).

To create an admin account, use the following command:

```bash
bin/magento admin:user:create
```

You can check the App Search extension is correctly installed and configured by connecting to the admin:
1. Go into `Stores --> Configuration`.
2. You should be able to find a `Èlastic App Search` tab into the General section
3. Click on it and see if your API Endpoint is correctly configured (should be disabled for edit if using env variable).
