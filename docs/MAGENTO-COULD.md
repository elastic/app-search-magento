# Using App Search with Magento Cloud

This documentation will help you to run App Search against your Magento Cloud project.

**Note:**

It is assumed that you have an App Search instance running and accessible from the Magento cloud instance.

If it is not yet the case, you should try to create an account [here](https://app.swiftype.com/signup).

## Configure your project to use App Search as search engine:

When using Magento Cloud, the search engine can not be changed from the admin or by using the `env.php` file.

Instead you have to set the `SEARCH_CONFIGURATION` variable into your project:

```bash
magento-cloud project:variable:set --json SEARCH_CONFIGURATION '{"engine":"elastic_appsearch"}'
```

## App Search module configuration:

Then you can set the App Search endpoint base URL and API keys by using:

```
magento-cloud project:variable:set --name CONFIG__DEFAULT__ELASTIC_APPSEARCH__CLIENT__API_ENDPOINT --value "https://host-XXXXX.api.swiftype.com"
magento-cloud project:variable:set --name CONFIG__DEFAULT__ELASTIC_APPSEARCH__CLIENT__PRIVATE_API_KEY --value "private-XXXXX"
magento-cloud project:variable:set --name CONFIG__DEFAULT__ELASTIC_APPSEARCH__CLIENT__SEARCH_API_KEY --value "search-XXXXX"
```

You may want to use a different configuration for each of your Magento Cloud environment (staging, production, ...). You can use the `variable:set` command to override the project configuration:

```
magento-cloud variable:set -e staging --name CONFIG__DEFAULT__ELASTIC_APPSEARCH__CLIENT__API_ENDPOINT --value "https://host-XXXXX.api.swiftype.com"
magento-cloud variable:set -e staging --name CONFIG__DEFAULT__ELASTIC_APPSEARCH__CLIENT__PRIVATE_API_KEY --value "private-XXXXX"
magento-cloud variable:set -e staging --name CONFIG__DEFAULT__ELASTIC_APPSEARCH__CLIENT__SEARCH_API_KEY --value "search-XXXXX"
```

## Environment config:

To avoid inconsistencies, you have to use a different engine for each environment you will deploy.
This can be achieved by configuring the engine prefix configuration that control how the engines created by the module are named.

```
magento-cloud variable:set -e staging --name CONFIG__DEFAULT__ELASTIC_APPSEARCH__CLIENT__ENGINE_PREFIX --value "mysite-staging"
```
