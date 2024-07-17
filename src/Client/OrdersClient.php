<?php

namespace DoubleBreak\Noverstock\Sdk\Client;

class OrdersClient extends AbstractClient
{
    public function __construct($config)
    {
        parent::__construct($config);
    }


    private function validateFiltersExist(
        array $filters,
        $failMessage = 'Invalid filters. One of `filter` or `search` keys are required'
    ): void {
        if (!isset($filters['filter']) && !isset($filters['search'])) {
            throw new \Exception($failMessage);
        }
    }


    /**
     * Orders list operation
     *
     * Example:
     * $client->getOrders([
     *   'filter' => [
     *     'filterDate' => [
     *       'createdOn' => '2021-01-01T00:00:00Z,2021-01-31T23:59:59Z',
     *     ],
     *   ]
     * ])
     *
     * Will return all orders created between 2021-01-01 and 2021-01-31
     **/
    public function getOrders(array $filters = [], array $params = []): array
    {
        $params['query'] = array_merge($params['query'] ?? [], $filters);

        $response = $this->request('GET', "{$this->baseUrl}/orders", $params);
        return $response;
    }


    /**
     * Creates new order operation
     **/
    public function createOrder(array $order = [], array $params = []): array
    {
        $params['json'] = $order;
        $response = $this->request('POST', "{$this->baseUrl}/orders", $params);
        return $response;
    }


    /**
     * Delete multiple orders operation
     *
     * Example:
     * $client->bulkDeleteOrders([
     *   'filter' => [
     *    'filterDate' => [
     *       'createdOn' => '2021-01-01T00:00:00Z,2021-01-31T23:59:59Z',
     *   ],
     *  'isReserved' => false
     * ],
     * 'search' => [
     *  'externalId' => '123456'
     * ]
     * ]);
     *
     * Will delete all orders that were not reserved and were created between 2021-01-01 and 2021-01-31 and externalId contains '123456'
     **/
    public function bulkDeleteOrders(array $filters, array $params = []): array
    {
        $this->validateFiltersExist(
            $filters,
            'Deleting orders without filters is dangerous. If you are sure you want to delete all orders, please use the generic request() method'
        );

        $params['json'] = [
            'filters' => http_build_query($filters),
        ];
        $response = $this->request('DELETE', "{$this->baseUrl}/orders", $params);
        return $response;
    }


    /**
     * Fetch Order entity by uuid operation
     **/
    public function getOrder($uuid, array $params = []): array
    {
        $params['path'] = array_merge($params['path'] ?? [], ['uuid' => $uuid]);
        $response = $this->request('GET', "{$this->baseUrl}/orders/{uuid}", $params);
        return $response;
    }


    /**
     * Update an Order entity by uuid operation
     **/
    public function updateOrder($uuid, array $order, $params = []): array
    {
        $params['path'] = array_merge($params['path'] ?? [], ['uuid' => $uuid]);
        $params['json'] = $order;
        $response = $this->request('PATCH', "{$this->baseUrl}/orders/{uuid}", $params);
        return $response;
    }

    /**
     * Update status of multiple orders at once. Affected orders are determined by provided filters operation
     **/
    public function bulkUpdateStatus($newStatus, array $filters, array $params = []): array
    {
        $this->validateFiltersExist(
            $filters,
            'Updating orders without filters is dangerous. If you are sure you want to update all orders, please use the generic request() method'
        );


        $params['json'] = [
            'newStatus' => $newStatus,
            'filters' => http_build_query($filters),
        ];

        $response = $this->request('PATCH', "{$this->baseUrl}/orders/status", $params);
        return $response;
    }


    /**
     * Create several orders at once operation
     **/
    public function bulkCreateOrders(array $orders = [], array $params = []): array
    {
        $params['json'] = $orders;
        $response = $this->request('POST', "{$this->baseUrl}/orders/batch-create", $params);
        return $response;
    }


    /**
     * Update several orders at once. Affected orders are determined by provided filters operation
     *
     * Example:
     * $client->bulkUpdateOrders(['warehouseUuid' => '8add57e6-770c-4fc8-9e9f-6e7a39621941'], [
     *    'filter' => [
     *      'filterDate' => [
     *          'updatedOn' => '2021-01-01T00:00:00Z,2021-01-31T23:59:59Z',
     *      ],
     *      'isReserved' => false
     *    ],
     *    'search' => [
     *      'externalId' => '123456'
     *    ]
     * ]);
     *
     *  Will set warehouseUuid to '8add57e6-770c-4fc8-9e9f-6e7a39621941' for all orders that were not reserved and were
     *  updated between 2021-01-01 and 2021-01-31 and externalId contains '123456'
     **/
    public function bulkUpdateOrders(array $fieldsToUpdate, array $filters): array
    {
        $this->validateFiltersExist(
            $filters,
            'Updating orders without filters is dangerous. If you are sure you want to update all orders, please use the generic request() method'
        );

        $params['json'] = [
            'fieldsToUpdate' => $fieldsToUpdate,
            'filters' => http_build_query($filters)
        ];
        $response = $this->request('PATCH', "{$this->baseUrl}/orders/batchUpdate", $params);
        return $response;
    }


    /**
     * Orders products list operation
     **/
    public function getOrderProducts($orderUuid, $params = []): array
    {
        $params['path'] = array_merge($params['path'] ?? [], ['orderUuid' => $orderUuid]);
        $response = $this->request('GET', "{$this->baseUrl}/orders/{orderUuid}/products", $params);
        return $response;
    }


    /**
     * Update order product collection operation
     **/
    public function updateOrderProducts($orderUuid, array $products = [], $params = []): array
    {
        $params['path'] = array_merge($params['path'] ?? [], ['orderUuid' => $orderUuid]);
        $params['json'] = ['productList' => $products];
        $response = $this->request('PATCH', "{$this->baseUrl}/orders/{orderUuid}/products", $params);
        return $response;
    }

    public function setOrderTags($orderUuid, array $tags = [], $params = []): array
    {
        $params['path'] = array_merge($params['path'] ?? [], ['orderUuid' => $orderUuid]);
        $params['json'] = $tags;

        $response = $this->request('PATCH', "{$this->baseUrl}/orders/{orderUuid}/tags", $params);
        return $response;
    }

    /**
     * Update tags of multiple orders at once. Affected orders are determined by provided filters operation
     *
     * Example:
     * $client->bulkUpdateOrdersTags(['tag1', 'tag2'], ['tag3'], 'SKIP', [
     *   'filter' => [
     *     'filterDate' => [
     *       'createdOn' => '2021-01-01T00:00:00Z,2021-01-31T23:59:59Z',
     *     ],
     *   ]
     * ]);
     *
     * Will add tags 'tag1' and 'tag2' and remove tag 'tag3' from all orders created between 2021-01-01 and 2021-01-31
     *
     *
     */
    public function bulkUpdateOrdersTags(
        array $tagsToToggleOn,
        array $tagsToToggleOff,
        array $filters,
        $onTagToggleResolverDenial = 'SKIP',
        array $params = []
    ) {
        if ($onTagToggleResolverDenial != 'SKIP' || $onTagToggleResolverDenial != 'FAIL_ALL') {
            throw new \Exception('Invalid value for onTagToggleResolverDenial');
        }

        $this->validateFiltersExist(
            $filters,
            'Updating orders tags without filters is dangerous. If you are sure you want to update all orders tags, please use the generic request() method'
        );

        $data = [
            'tagsToToggleOn' => $tagsToToggleOn,
            'tagsToToggleOff' => $tagsToToggleOff,
            'onTagToggleResolverDenial' => $onTagToggleResolverDenial,
        ];

        $params['json'] = [
            'data' => $data,
            'filters' => http_build_query($filters),
        ];

        $response = $this->request('PATCH', "{$this->baseUrl}/orders/tags", $params);
        return $response;
    }
}


