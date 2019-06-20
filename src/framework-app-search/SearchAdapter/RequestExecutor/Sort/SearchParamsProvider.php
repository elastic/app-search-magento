<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\Framework\AppSearch\SearchAdapter\RequestExecutor\Sort;

use Elastic\AppSearch\Framework\AppSearch\SearchAdapter\RequestExecutor\SearchParamsProviderInterface;
use Magento\Framework\Search\RequestInterface;
use Elastic\AppSearch\Framework\AppSearch\Engine\SchemaInterface;
use Elastic\AppSearch\Framework\AppSearch\Engine\Field\FieldMapperResolverInterface;
use Elastic\AppSearch\Framework\AppSearch\Engine\Field\FieldMapperInterface;

/**
 * Sort order search params.
 *
 * @package   Elastic\AppSearch\Framework\AppSearch\SearchAdapter\RequestExecutor\Sort
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class SearchParamsProvider implements SearchParamsProviderInterface
{
    /**
     * @var string
     */
    const SORT_PARAM_NAME = 'sort';

    /**
     * @var FieldMapperResolverInterface
     */
    private $fieldMapperResolver;

    /**
     * Constructor.
     *
     * @param FieldMapperResolverInterface $fieldMapperResolver
     */
    public function __construct(FieldMapperResolverInterface $fieldMapperResolver)
    {
        $this->fieldMapperResolver = $fieldMapperResolver;
    }

    /**
     * {@inheritDoc}
     */
    public function getParams(RequestInterface $request): array
    {
        $sorts = [];

        if ($this->canSort($request)) {
            foreach ($request->getSort() as $sortOrder) {
                $fieldName = $this->getFieldName($request->getIndex(), $sortOrder->getField() ?? '_score');
                $sorts[] = [$fieldName => strtolower($sortOrder->getDirection() ?: 'desc')];
            }
        }

        if (count($sorts) == 1 && isset(current($sorts)['_score']) && current($sorts)['_score'] == 'desc') {
            $sorts = [];
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
    private function getFieldName(string $indexIdentifier, string $requestFieldName): string
    {
        $fieldName = $requestFieldName === 'score' ? '_score' : $requestFieldName;

        if ($fieldName !== '_score') {
            $context   = ['type' => SchemaInterface::CONTEXT_SORT];
            $fieldName = $this->getFieldMapper($indexIdentifier)->getFieldName($requestFieldName, $context);
        }

        return $fieldName;
    }

    /**
     * Retrive field mapper for the current request.
     *
     * @param string $indexIdentifier
     *
     * @return FieldMapperInterface
     */
    private function getFieldMapper(string $indexIdentifier): FieldMapperInterface
    {
        return $this->fieldMapperResolver->getFieldMapper($indexIdentifier);
    }
}
