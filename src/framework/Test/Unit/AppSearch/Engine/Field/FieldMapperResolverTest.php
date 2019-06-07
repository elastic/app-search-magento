<?php
/*
 * This file is part of the App Search Magento module.
 *
 * (c) Elastic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elastic\AppSearch\Framework\Test\Unit\AppSearch\Engine\Field;

use Elastic\AppSearch\Framework\AppSearch\Engine\Field\FieldMapperResolver;
use Elastic\AppSearch\Framework\AppSearch\Engine\Field\FieldMapperInterface;

/**
 * Unit test for the Elastic\AppSearch\Framework\AppSearch\Engine\Field\FieldMapperResolver class.
 *
 * @package   Elastic\AppSearch\Framework\Test\Unit\AppSearch\Engine\\Field
 * @copyright 2019 Elastic
 * @license   Open Software License ("OSL") v. 3.0
 */
class FieldMapperResolverTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Test field mapper resolution.
     */
    public function testGetFieldMapper()
    {
        $resolver = new FieldMapperResolver(['foo' => $this->createMock(FieldMapperInterface::class)]);

        $this->assertInstanceOf(FieldMapperInterface::class, $resolver->getFieldMapper('foo'));
    }

    /**
     * Test field mapper resolution.
     *
     * @expectedException \Magento\Framework\Exception\LocalizedException
     */
    public function testGetInvalidFieldMapper()
    {
        $resolver = new FieldMapperResolver();
        $resolver->getFieldMapper('foo');
    }
}
