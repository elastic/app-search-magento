/**
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package   Elastic\AppSearch\CatalogSearch
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */

define(['jquery'], function ($) {

  class Headers {
    constructor(config) {
      this.config = Object.assign({}, config);
    }
    getHeaders() {
      return {"Authorization": "Bearer " + this.config.apiKey};
    }
  }

  class Url {
    constructor(config, endpoint) {
      this.config = Object.assign({}, config);
      this.config.endpoint = endpoint;
    }
    getUrl() {
      return this.config.apiEndpoint + '/api/as/v1/engines/' + this.config.engineName + '/' + this.config.endpoint;
    }
  }

  class SearchQuery {
      constructor(config) {
        this.config  = Object.assign({}, config);
        this.headers = new Headers(config);
        this.url     = new Url(config, 'search');
      }
      run() {
        var postData = {'query': this.config.query, "analytics": {"tags": [this.config.analyticsTag]}};
        var url      = this.url.getUrl();
        var headers  = this.headers.getHeaders();
        $.ajax({'type': 'POST', 'url': url, 'data': postData, 'headers': headers});
      }
  }

  class Tracker {
    constructor(config) {
      this.config  = Object.assign({}, config);
      this.headers = new Headers(config);
      this.url     = new Url(config, 'click');

      this.installCallbacks();
      if (this.config.doSearch) {
        new SearchQuery(config).run();
      }
    }
    getPostData(documentId) {
      return {'query': this.config.query, 'document_id': documentId, "tags": [this.config.analyticsTag]};
    }
    sendClick(documentId) {
      var postData = this.getPostData(documentId);
      var url      = this.url.getUrl();
      var headers  = this.headers.getHeaders();
      $.ajax({'type': 'POST', 'url': url, 'data': postData, 'headers': headers});
    }
    installCallbacks() {
      if (this.config.itemsSelector) {
        $(this.config.itemsSelector).each(function(index, productListItemNode) {
          var documentId = this.getDocumentId(productListItemNode);
          if (documentId) {
            $(productListItemNode).find(this.config.linksSelector).click(function (ev) {
              this.sendClick(documentId);
            }.bind(this));
          }
        }.bind(this));
      }
    }
    getDocumentId(rootNode) {
      return $(rootNode).find(this.config.documentIdNodeSelector).attr(this.config.documentIdAttribute);
    }
  }

  return function(config) { return new Tracker(config); };
});
