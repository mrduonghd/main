<?php
namespace Mpx\Sales\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface;
use Webkul\Marketplace\Model\SellerFactory;
use Webkul\Marketplace\Helper\Data;
use Magento\Store\Model\StoreManagerInterface;
use Mpx\Sales\Helper\Data as MpxSalesHelperData;

class SetEmailVariableSellerOrder implements ObserverInterface
{
    /**
     * @var SellerFactory
     */
    protected $seller;

    /**
     * @var Data
     */
    protected $data;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var MpxSalesHelperData
     */
    protected $mpxSalesHelperData;

    /**
     * @param SellerFactory $seller
     * @param Data $data
     * @param StoreManagerInterface $storeManager
     * @param LoggerInterface $logger
     * @param MpxSalesHelperData $mpxSalesHelperData
     */
    public function __construct(
        SellerFactory $seller,
        Data $data,
        StoreManagerInterface $storeManager,
        LoggerInterface $logger,
        MpxSalesHelperData $mpxSalesHelperData
    ) {
        $this->seller = $seller;
        $this->data = $data;
        $this->storeManager = $storeManager;
        $this->logger = $logger;
        $this->mpxSalesHelperData = $mpxSalesHelperData;
    }

    /**
     * Set data shop url in mail new order
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer) :void
    {
        $transportObject = $observer->getData('transportObject');
        $productId = $transportObject->getOrder()->getItems()[0]->getProductId();
        $sellerId = $this->data->getSellerIdByProductId($productId);
        $seller = $this->seller->create()->getCollection()
            ->addFieldToFilter('seller_id', $sellerId)
            ->addFieldToFilter('store_id', 1)
            ->getFirstItem();
        $shopTitle = $seller->getShopTitle();
        $shopUrl = $seller->getShopUrl();
        $transportObject['shop_title'] = $shopTitle ?? '';
        $transportObject['shop_page_url'] = $this->mpxSalesHelperData->getUrl($shopUrl ?? '');
    }
}
