<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\Framework\AppSearch\Engine\Field\Config;

use Elastic\AppSearch\Framework\AppSearch\Engine\Field\FieldInterface;

/**
 * A simple field interface implementation.
 *
 * @package   Elastic\AppSearch\Framework\AppSearch\Engine\Field\Config
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class Field implements FieldInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $type;

    /**
     * Constructor.
     *
     * @param string $name
     * @param string $type
     */
    public function __construct(string $name, string $type)
    {
        $this->name = $name;
        $this->type = $type;
    }

    /**
     * {@inheritDoc}
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * {@inheritDoc}
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * {@inheritDoc}
     */
    public function isSearchable(): bool
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function isFilterable(): bool
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function isSortable(): bool
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function useValueField(): bool
    {
        return false;
    }
}
