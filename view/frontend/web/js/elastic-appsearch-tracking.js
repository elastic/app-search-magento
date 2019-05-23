/**
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package   Elastic\AppSearch
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */

define(['jquery'], function ($) {

  class Tracker {
    constructor(config) {
      this.config = config;
      this.installCallbacks();
    }
    getUrl() {
      return this.config.apiEndpoint + '/api/as/v1/engines/' + this.config.engineName + '/click';
    }
    getHeaders() {
      return {"Authorization": "Bearer " + this.config.apiKey};
    }
    getPostData(documentId) {
      return {'query': this.config.query, 'document_id': documentId};
    }
    sendClick(documentId) {
      var postData = this.getPostData(documentId);
      var url      = this.getUrl();
      var headers  = this.getHeaders();

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
