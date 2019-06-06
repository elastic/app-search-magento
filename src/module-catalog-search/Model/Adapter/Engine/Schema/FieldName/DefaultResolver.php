<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\CatalogSearch\Model\Adapter\Engine\Schema\FieldName;

use Elastic\AppSearch\Framework\AppSearch\Engine\Field\FieldNameResolver as FrameworkFieldNameResolver;
use Elastic\AppSearch\Framework\AppSearch\Engine\Field\AttributeAdapterInterface;
use Magento\Customer\Model\Session as CustomerSession;

/**
 * Customize default field name resolver for products.
 *
 * @package   Elastic\Model\Adapter\Engine
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class DefaultResolver extends FrameworkFieldNameResolver
{
    /**
     * @var CustomerSession
     */
    private $customerSession;

    /**
     * Constructor
     *
     * @param CustomerSession $customerSession
     */
    public function __construct(CustomerSession $customerSession)
    {
        $this->customerSession = $customerSession;
    }

    /**
     * {@inheritDoc}
     */
    public function getFieldName(AttributeAdapterInterface $attribute, array $context = []): string
    {
        $fieldName = parent::getFieldName($attribute, $context);

        if ($fieldName == 'price') {
            $fieldName = $this->getPriceFieldName($fieldName, $context);
        }

        return $fieldName;
    }

    /**
     * Add customer group id to the field name.
     *
     * @deprecated Will be replace by a specific product resolver in the future.
     *
     * @param string $fieldName Original field name
     * @param array  $context
     *
     * @return string
     */
    private function getPriceFieldName(string $fieldName, array $context)
    {
        $groupId = $context['customer_group_id'] ?? $this->customerSession->getCustomerGroupId();

        if ($groupId) {
            $fieldName = $fieldName . '_' . $groupId;
        }

        return $fieldName;
    }
}
