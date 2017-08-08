<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\InventoryApi\Test\Api\SourceRepository;

use Magento\Framework\Api\SortOrder;
use Magento\Framework\Webapi\Rest\Request;
use Magento\InventoryApi\Api\Data\SourceInterface;
use Magento\TestFramework\Assert\AssertArrayContains;
use Magento\TestFramework\TestCase\WebapiAbstract;

class GetListTest extends WebapiAbstract
{
    /**#@+
     * Service constants
     */
    const SOURCES_PATH = '/V1/inventory/sources';
    const SERVICE_NAME = 'inventorySourceRepositoryV1';
    /**#@-*/

    /**
     * @magentoApiDataFixture ../../../../app/code/Magento/InventoryApi/Test/_files/source/source_list.php
     * @param array $searchCriteria
     * @param array $expectedItemsData
     * @dataProvider dataProviderGetList
     */
    public function testGetList(array $searchCriteria, array $expectedItemsData)
    {
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::SOURCES_PATH . '?' . http_build_query(['searchCriteria' => $searchCriteria]),
                'httpMethod' => Request::HTTP_METHOD_GET,
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'operation' => self::SERVICE_NAME . 'GetList',
            ],
        ];
        $response = $this->_webApiCall($serviceInfo);

        self::assertEquals(count($expectedItemsData), $response['total_count']);
        AssertArrayContains::assert($searchCriteria, $response['search_criteria']);
        AssertArrayContains::assert($expectedItemsData, $response['items']);
    }

    /**
     * @return array
     */
    public function dataProviderGetList()
    {
        return [
            'filtering_by_field' => [
                [
                    'filter_groups' => [
                        [
                            'filters' => [
                                [
                                    'field' => SourceInterface::ENABLED,
                                    'value' => 0,
                                    'condition_type' => 'eq',
                                ],
                            ],
                        ],
                    ],
                    'sort_orders' => [
                        [
                            'field' => SourceInterface::PRIORITY,
                            'direction' => SortOrder::SORT_DESC,
                        ],
                    ],
                ],
                [
                    [
                        SourceInterface::NAME => 'source-name-3',
                    ],
                    [
                        SourceInterface::NAME => 'source-name-4',
                    ],
                ],
            ],
            'ordering_by_field' => [
                [
                    'sort_orders' => [
                        [
                            'field' => SourceInterface::PRIORITY,
                            'direction' => SortOrder::SORT_DESC,
                        ],
                        [
                            'field' => SourceInterface::NAME,
                            'direction' => SortOrder::SORT_DESC,
                        ],
                    ],
                ],
                [
                    [
                        SourceInterface::NAME => 'source-name-1',
                    ],
                    [
                        SourceInterface::NAME => 'source-name-3',
                    ],
                    [
                        SourceInterface::NAME => 'source-name-2',
                    ],
                    [
                        SourceInterface::NAME => 'source-name-4',
                    ],
                ],
            ],
        ];
    }
}
