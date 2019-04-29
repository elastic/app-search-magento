<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\SearchAdapter\Request\Sort;

use Elastic\AppSearch\SearchAdapter\Request\SearchParamsProviderInterface;
use Magento\Framework\Search\RequestInterface;
use Elastic\AppSearch\Model\Adapter\Engine\Schema\FieldNameResolverInterface;
use Elastic\AppSearch\Model\Adapter\Engine\Schema\AttributeAdapterProvider;
use Elastic\AppSearch\Model\Adapter\Engine\SchemaInterface;
use Elastic\AppSearch\Model\Adapter\Engine\Schema\AttributeAdapter;

/**
 * Sort order search params.
 *
 * @package   Elastic\AppSearch\SearchAdapter\Request\Sort
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class SearchParamsProvider implements SearchParamsProviderInterface
{
    /**
     * @var string
     */
    private const SORT_PARAM_NAME = 'sort';

    /**
     * @var FieldNameResolverInterface
     */
    private $fieldNameResolver;

    /**
     * @var AttributeAdapterProvider
     */
    private $attributeProvider;

    /**
     * Constructor.
     *
     * @param AttributeAdapterProvider   $attributeProvider
     * @param FieldNameResolverInterface $fieldNameResolver
     */
    public function __construct(
        AttributeAdapterProvider $attributeProvider,
        FieldNameResolverInterface $fieldNameResolver
    ) {
        $this->attributeProvider = $attributeProvider;
        $this->fieldNameResolver = $fieldNameResolver;
    }

    /**
     * {@inheritDoc}
     */
    public function getParams(RequestInterface $request): array
    {
        $sorts = null;

        if ($this->canSort($request)) {
            foreach ($request->getSort() as $sortOrder) {
                $fieldName = $this->getFieldName($sortOrder->getField());
                $sorts[] = [$fieldName => strtolower($sortOrder->getDirection())];
            }
        }

        return !empty($sorts) ? [self::SORT_PARAM_NAME => $sorts] : [];
    }

    /**
     * Indicate if the request can be used for sorting.
     *
     * @param RequestInterface $request
     *
     * @return bool
     */
    private function canSort(RequestInterface $request): bool
    {
        return method_exists($request, 'getSort') && $request->getSort();
    }

    /**
     * Convert the field name to match the indexed data.
     *
     * @param string $requestFieldName
     *
     * @return string
     */
    private function getFieldName(string $requestFieldName): string
    {
        $fieldName = $this->fieldNameResolver->getFieldName(
            $this->getAttribute($requestFieldName),
            ['type' => SchemaInterface::CONTEXT_SORT]
        );

        return $fieldName === 'score' ? '_score' : $fieldName;
    }

    /**
     * Retrieve attribute to be used for sorting.
     *
     * @param string $requestFieldName
     *
     * @return AttributeAdapter
     */
    private function getAttribute(string $requestFieldName): AttributeAdapter
    {
        return $this->attributeProvider->getAttributeAdapter($requestFieldName);
    }
}
