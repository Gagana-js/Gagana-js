<?php

declare(strict_types=1);

namespace StarHangar\TestTask\Cron;

use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;
use Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory as OrderItemCollectionFactory;
use Magento\Sales\Api\OrderRepositoryInterface as OrderRepositoryFactory;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\DateTime;

class CancelOrders
{
    /**
     * @var OrderCollectionFactory
     */
    protected $orderCollectionFactory;

    /**
     * @var OrderItemCollectionFactory
     */
    protected $orderItemCollectionFactory;

    /**
     * @var OrderRepositoryFactory
     */
    protected $orderRepositoryFactory;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var DateTime
     */
    protected $dateTime;

    /**
     * CancelOrders constructor.
     *
     * @param OrderCollectionFactory     $orderCollectionFactory
     * @param OrderItemCollectionFactory $orderItemCollectionFactory
     * @param OrderRepositoryFactory     $orderRepositoryFactory
     * @param Registry                   $registry
     * @param DateTime                   $dateTime
     */
    public function __construct(
        OrderCollectionFactory $orderCollectionFactory,
        OrderItemCollectionFactory $orderItemCollectionFactory,
        OrderRepositoryFactory $orderRepositoryFactory,
        Registry $registry,
        DateTime $dateTime
    ) {
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->orderItemCollectionFactory = $orderItemCollectionFactory;
        $this->orderRepositoryFactory = $orderRepositoryFactory;
        $this->registry = $registry;
        $this->registry->register('isSecureArea', true);
        $this->dateTime = $dateTime;
    }

    /**
     * Execute the cron job to cancel orders.
     *
     * @return $this
     */
    public function execute()
    {
        // Get the current GMT date and time
        $currentDateTime = $this->dateTime->gmtDate();
        // Calculate the date and time 6 hours ago
        $olderThan = date('Y-m-d H:i:s', strtotime($currentDateTime) - 130);

        // Create order collection
        $orderCollection = $this->orderCollectionFactory->create();
        $orderCollection->getSelect()->joinLeft(
            ['soi' => $orderCollection->getTable('sales_order_item')],
            'main_table.entity_id = soi.order_id',
            []
        );

        // Filter orders based on product type and status
        $orderCollection
            ->addFieldToFilter('soi.product_type', 'product_type_starhangar_ship')
            ->addFieldToFilter('status', ['processing', 'pending']);

        // Iterate through the filtered orders
        foreach ($orderCollection as $order) {
            $orderCreatedAt = $order->getCreatedAt();

            // Convert order creation time to GMT
            $orderCreatedAtGMT = $this->dateTime->gmtDate(null, $orderCreatedAt);

            // Check if the order creation time is older than the specified threshold
            if ($orderCreatedAtGMT < $olderThan) {
                $orderId = $order->getId();
                // Get the order instance by ID
                $orderInstance = $this->orderRepositoryFactory->get($orderId);
                // Delete the order
                $this->orderRepositoryFactory->delete($orderInstance);
            }
        }

        return $this;
    }
}
