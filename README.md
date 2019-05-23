<p align="center">![Elastic App Search Logo](docs/assets/logo-app-search.png)</p>

<p align="center"><a href="https://circleci.com/gh/swiftype/swiftype-app-search-magento"><img src="https://circleci.com/gh/swiftype/swiftype-app-search-magento.svg?style=svg&circle-token=f396f44f6e5dbcced1d3d8e3b42bcee791b805a0" alt="CircleCI build"></a></p>

> A first-party Magento integration for building excellent, relevant search experiences with [Elastic App Search](https://www.elastic.co/cloud/app-search-service).
>
> **:warning: This is a beta version of the module. Stable version will be released soon.**

## Contents

- [Getting started](#getting-started-)
- [Usage](#usage)
- [Development](#development)
- [FAQ](#faq-)
- [Contribute](#contribute-)
- [License](#license-)

***

## Getting started 🐣

Using this client assumes that you have already:

- Created an App Search account on https://swiftype.com/ or you have a self managed version of App Search available

- Magento >= 2.2.x installed and running

You can install the module in your project by using this composer command from your Magento project root:

```bash
composer require elastic/elastic-app-search-magento
```

## Usage

### Configuring App Search credentials

To configure your credentials, you will need to collect the following information:

- Your App Search API endpoint
- Your App Search Private API key
- Your App Search Public Search key

If using a swiftype.com account, you will be able to access this information at [https://app.swiftype.com/as/credentials](https://app.swiftype.com/as/credentials).

On-premises user should connect to their App Search instance to retrieve their credentials.

#### From Magento Admin

You can provide your credentials from Magento Admin by browsing the **Stores > Configuration > General > Elastic App Search** section:

![App Search Credentials Config](docs/assets/credentials.png)

**Note:**
The module will create one App Search engine per store view.
This engines will use a prefix that can be configured with the client (e.g. magento2-catalogfulltext-search-1).

If you plan to use the same account for several environment, update this setting (e.g. my-website-staging).

#### From Magento CLI

You can update setting from Magento CLI by using:

```bash
bin/magento config:set elastic_appsearch/client/api_endpoint "https://host-xxxx.api.swiftype.com"
bin/magento config:set elastic_appsearch/client/private_api_key "private-XXXXX"
bin/magento config:set elastic_appsearch/client/search_api_endpoint "search-XXXXX"
```

You can additionally set the engine prefix by using:
```bash
bin/magento config:set elastic_appsearch/client/engine_prefix "my-website-staging"
```

### Configuring App Search as Magento default search engine

### Reindexing content

## Development

## FAQ 🔮

### Where do I report issues with the client?

If something is not working as expected, please open an [issue](https://github.com/swiftype/swiftype-app-search-magento/issues/new).

### Where can I find the full App Search API documentation ?

Your best bet is to read the [documentation](https://swiftype.com/documentation/app-search).

### Where else can I go to get help?

You can checkout the [Elastic community discuss forums](https://discuss.elastic.co/c/app-search).

## Contribute 🚀

We welcome contributors to the project. Before you begin, a couple notes...

+ Before opening a pull request, please create an issue to [discuss the scope of your proposal](https://github.com/swiftype/swiftype-app-search-magento/issues).
+ Please write simple code and concise documentation, when appropriate.

## License 📗

[Open Software License ("OSL") v.3.0](LICENSE) © [Elastic](https://github.com/elastic)

Thank you to all the [contributors](https://github.com/swiftype/swiftype-app-search-magento/graphs/contributors)!
