<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_MpAssignProduct
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\MpAssignProduct\Helper;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\UrlInterface;
use Webkul\Marketplace\Model\ResourceModel\Product\CollectionFactory;
use Webkul\Marketplace\Model\ResourceModel\Seller\CollectionFactory as SellerCollection;
use Webkul\MpAssignProduct\Model\ResourceModel\Items\CollectionFactory as ItemsCollection;
use Webkul\MpAssignProduct\Model\ResourceModel\Quote\CollectionFactory as QuoteCollection;
use Webkul\MpAssignProduct\Model\ResourceModel\Data\CollectionFactory as DataCollection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollection;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Framework\App\Area;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable as ConfigurableCollection;
use Magento\Catalog\Api\ProductRepositoryInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $_allowedProductTypes = ['simple', 'virtual', 'configurable'];
    /**
     * @var templateId
     */
    protected $_template;
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $_messageManager;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $_customer;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $_filesystem;

    /**
     * @var \Magento\Framework\Data\Form\FormKey
     */
    protected $_formKey;

    /**
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    protected $_currency;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $_resource;

    /**
     * @var \Magento\MediaStorage\Model\File\UploaderFactory
     */
    protected $_fileUploader;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_product;

    /**
     * @var \Magento\Checkout\Model\Cart
     */
    protected $_cart;

    /**
     * @var \Webkul\Marketplace\Model\ProductFactory
     */
    protected $_mpProduct;

    /**
     * @var \Webkul\MpAssignProduct\Model\ItemsFactory
     */
    protected $_items;

    /**
     * @var \Webkul\MpAssignProduct\Model\DataFactory
     */
    protected $_data;

    /**
     * @var CollectionFactory
     */
    protected $_mpProductCollection;

    /**
     * @var SellerCollection
     */
    protected $_sellerCollection;

    /**
     * @var ItemsCollection
     */
    protected $_itemsCollection;

    /**
     * @var QuoteCollection
     */
    protected $_quoteCollection;

    /**
     * @var DataCollection
     */
    protected $_dataCollection;

    /**
     * @var ProductCollection
     */
    protected $_productCollection;

    /**
     * @var \Webkul\MpAssignProduct\Model\AssociatesFactory
     */
    protected $_associates;

    /**
     * @var \Magento\Quote\Model\Quote\Item\OptionFactory
     */
    protected $_quoteOption;

    /**
     * @var \Magento\CatalogInventory\Api\StockRegistryInterface
     */
    protected $_stockRegistry;

    /**
     * @var TransportBuilder
     */
    protected $_transportBuilder;

    /**
     * @var StateInterface
     */
    protected $_inlineTranslation;

    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    protected $_priceCurrency;

    /**
     * @var \Magento\Framework\Filesystem\Driver\File
     */
    protected $_fileDriver;

    /**
     * @var ConfigurableCollection
     */
    protected $_configurableCollection;

    /**
     * @var Option
     */
    protected $_customOptions;

    /**
     * @var Logger
     */
    protected $_logger;

    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $_moduleManager;

    /**
     * @var \Magento\CatalogInventory\Api\StockStateInterface
     */
    protected $stockStateInterface;

    /**
     * @var \Magento\Framework\Escaper
     */
    protected $escaper;

    /**
     * @var \Magento\Catalog\Model\Product\Action
     */
    protected $productAction;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Customer\Model\CustomerFactory $customer
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\Data\Form\FormKey $formKey
     * @param \Magento\Framework\Pricing\Helper\Data $currency
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Checkout\Model\Cart $cart
     * @param \Webkul\Marketplace\Model\ProductFactory $mpProductFactory
     * @param \Webkul\MpAssignProduct\Model\ItemsFactory $itemsFactory
     * @param \Webkul\MpAssignProduct\Model\DataFactory $dataFactory
     * @param \Webkul\MpAssignProduct\Model\AssociatesFactory $associatesFactory
     * @param \Magento\Quote\Model\Quote\Item\OptionFactory $quoteOption
     * @param CollectionFactory $mpProductCollectionFactory
     * @param SellerCollection $sellerCollectionFactory
     * @param ItemsCollection $itemsCollectionFactory
     * @param QuoteCollection $quoteCollectionFactory
     * @param DataCollection $dataCollectionFactory
     * @param ProductCollection $productCollectionFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry
     * @param TransportBuilder $transportBuilder
     * @param StateInterface $inlineTranslation
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param \Magento\Framework\Filesystem\Driver\File $fileDriver
     * @param ConfigurableCollection $configurableCollection
     * @param \Magento\Catalog\Model\Product\Option $customOptions
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param \Magento\CatalogInventory\Api\StockStateInterface $stockStateInterface
     * @param \Magento\Framework\Escaper $escaper
     * @param \Magento\Catalog\Model\Product\Action $productAction
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Model\CustomerFactory $customer,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Data\Form\FormKey $formKey,
        \Magento\Framework\Pricing\Helper\Data $currency,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Checkout\Model\Cart $cart,
        \Webkul\Marketplace\Model\ProductFactory $mpProductFactory,
        \Webkul\MpAssignProduct\Model\ItemsFactory $itemsFactory,
        \Webkul\MpAssignProduct\Model\DataFactory $dataFactory,
        \Webkul\MpAssignProduct\Model\AssociatesFactory $associatesFactory,
        \Magento\Quote\Model\Quote\Item\OptionFactory $quoteOption,
        CollectionFactory $mpProductCollectionFactory,
        SellerCollection $sellerCollectionFactory,
        ItemsCollection $itemsCollectionFactory,
        QuoteCollection $quoteCollectionFactory,
        DataCollection $dataCollectionFactory,
        ProductCollection $productCollectionFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        TransportBuilder $transportBuilder,
        StateInterface $inlineTranslation,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Framework\Filesystem\Driver\File $fileDriver,
        ConfigurableCollection $configurableCollection,
        \Magento\Catalog\Model\Product\Option $customOptions,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\CatalogInventory\Api\StockStateInterface $stockStateInterface,
        \Magento\Framework\Escaper $escaper,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute $eavAttribute,
        \Magento\Catalog\Model\Product\Action $productAction,
        ProductRepositoryInterface $productRepository = null
    ) {
        $this->_scopeConfig = $context->getScopeConfig();
        $this->_request = $context->getRequest();
        $this->_coreRegistry = $coreRegistry;
        $this->_storeManager = $storeManager;
        $this->_messageManager = $messageManager;
        $this->_customerSession = $customerSession;
        $this->_customer = $customer;
        $this->_filesystem = $filesystem;
        $this->_formKey = $formKey;
        $this->_currency = $currency;
        $this->_resource = $resource;
        $this->_fileUploader = $fileUploaderFactory;
        $this->_product = $productFactory;
        $this->_cart = $cart;
        $this->_mpProduct = $mpProductFactory;
        $this->_items = $itemsFactory;
        $this->_data = $dataFactory;
        $this->_mpProductCollection = $mpProductCollectionFactory;
        $this->_sellerCollection = $sellerCollectionFactory;
        $this->_itemsCollection = $itemsCollectionFactory;
        $this->_quoteCollection = $quoteCollectionFactory;
        $this->_dataCollection = $dataCollectionFactory;
        $this->_productCollection = $productCollectionFactory;
        $this->_associates = $associatesFactory;
        $this->_quoteOption = $quoteOption;
        $this->_stockRegistry = $stockRegistry;
        $this->_transportBuilder = $transportBuilder;
        $this->_inlineTranslation = $inlineTranslation;
        $this->_priceCurrency = $priceCurrency;
        $this->_fileDriver = $fileDriver;
        $this->_configurableCollection = $configurableCollection;
        $this->_customOptions = $customOptions;
        $this->_logger = $context->getLogger();
        $this->_moduleManager = $moduleManager;
        $this->stockStateInterface = $stockStateInterface;
        $this->escaper = $escaper;
        $this->eavAttribute = $eavAttribute;
        $this->productAction = $productAction;
        $this->productRepository = $productRepository
            ?: \Magento\Framework\App\ObjectManager::getInstance()->create(ProductRepositoryInterface::class);
        parent::__construct($context);
    }

    /**
     * Get Show Lower Price Setting Config
     *
     * @return bool
     */
    public function showMinimumPrice()
    {
        $config = 'mpassignproduct/settings/minimun';
        $showMinimum = $this->_scopeConfig->getValue($config);
        if ($showMinimum == '') {
            return false;
        }
        return $showMinimum;
    }

    /**
     * Get Assign Type Setting Config
     *
     * @return bool
     */
    public function getAssignType()
    {
        $config = 'mpassignproduct/settings/assign';
        return $this->_scopeConfig->getValue($config);
    }

    /**
     * Get Add Approve Product Setting Config
     *
     * @return bool
     */
    public function isAddApprovalRequired()
    {
        $config = 'mpassignproduct/settings/add_product';
        return $this->_scopeConfig->getValue($config);
    }

    /**
     * Get Edit Approve Product Setting Config
     *
     * @return bool
     */
    public function isEditApprovalRequired()
    {
        $config = 'mpassignproduct/settings/edit_product';
        return $this->_scopeConfig->getValue($config);
    }

    /**
     * Get Current Customer Id
     *
     * @return int
     */
    public function getCustomerId()
    {
        $customerId = 0;
        if ($this->_customerSession->isLoggedIn()) {
            $customerId = (int) $this->_customerSession->getCustomerId();
        }
        return $customerId;
    }

    /**
     * Check Customer is Logged In or Not
     *
     * @return bool
     */
    public function isLoggedIn()
    {
        if ($this->_customerSession->isLoggedIn()) {
            return true;
        }
        return false;
    }

    /**
     * Get Mediad Path
     *
     * @return string
     */
    public function getMediaPath()
    {
        return $this->_filesystem
                    ->getDirectoryRead(DirectoryList::MEDIA)
                    ->getAbsolutePath();
    }

    /**
     * Get Form Key
     *
     * @return string
     */
    public function getFormKey()
    {
        return $this->_formKey->getFormKey();
    }

    /**
     * Get Assign Product by AssignId
     *
     * @param int $assignId
     *
     * @return object
     */
    public function getAssignProduct($assignId)
    {
        $assignProduct = $this->_items->create()->load($assignId);
        return $assignProduct;
    }

    /**
     * Get Assign Product Collection
     *
     * @return collection object
     */
    public function getCollection()
    {
        $collection = $this->_itemsCollection->create();
        return $collection;
    }

    /**
     * Get Assign Product Quote Items Collection
     *
     * @return collection object
     */
    public function getQuoteCollection()
    {
        $collection = $this->_quoteCollection->create();
        return $collection;
    }

    /**
     * Get Product Collection
     *
     * @return collection object
     */
    public function getProductCollection()
    {
        $collection = $this->_productCollection->create();
        return $collection;
    }

    /**
     * Get Marketplace Product Collection
     *
     * @return collection object
     */
    public function getMpProductCollection()
    {
        $collection = $this->_mpProductCollection->create();
        return $collection;
    }

    /**
     * Get Cart
     *
     * @return object
     */
    public function getCart()
    {
        $cartModel = $this->_cart;
        return $cartModel;
    }

    /**
     * Get Current Product Id
     *
     * @return int
     */
    public function getProductId()
    {
        $id = (int) $this->_request->getParam('id');
        return $id;
    }

    /**
     * Get Product
     *
     * @param int $productId [optional]
     *
     * @return object
     */
    public function getProduct($productId = 0)
    {
        if ($productId == 0) {
            $productId = $this->getProductId();
        }
        $product = $this->_product->create()->load($productId);
        return $product;
    }

    /**
     * Get Searched Query String
     *
     * @return string
     */
    public function getQueryString()
    {
        $queryString = $this->_request->getParam('query');
        $queryString = strip_tags(trim($queryString));
        return str_replace('%', '', $queryString);
    }

    /**
     * Check Whether Product Is Valid Or Not.
     *
     * @param int $isAdd [optional]
     *
     * @return bool
     */
    public function checkProduct($isAdd = 0)
    {
        $result = ['msg' => '', 'error' => 0];
        $assignId = (int) $this->_request->getParam('id');
        if ($assignId == 0) {
            $result['error'] = 1;
            $result['msg'] = 'Invalid request.';
            return $result;
        }
        if ($isAdd == 1) {
            $productId = $assignId;
        } else {
            $assignData = $this->getAssignDataByAssignId($assignId);
            $productId = $assignData->getProductId();
        }
        $product = $this->getProduct($productId);
        if ($product->getId() <= 0) {
            $result['error'] = 1;
            $result['msg'] = 'Product does not exist.';
            return $result;
        }
        $productType = $product->getTypeId();
        $allowedProductTypes = $this->getAllowedProductTypes();
        if (!in_array($productType, $allowedProductTypes)) {
            $result['error'] = 1;
            $result['msg'] = 'Product type not allowed.';
            return $result;
        }
        $sellerId = $this->getSellerIdByProductId($productId);

        $customerId = $this->getCustomerId();
        if ($sellerId == $customerId) {
            $result['error'] = 1;
            $result['msg'] = 'Product is your own product.';
            return $result;
        }
        if ($isAdd == 1) {
            $assignId = $this->getAssignId($productId, $customerId);
            if ($assignId > 0) {
                $result['error'] = 1;
                $result['msg'] = 'Already assigned to you.';
                return $result;
            }
        }
        return $result;
    }

    /**
     * Return Assign Id by Product Id
     *
     * @param int $productId
     * @param int $sellerId
     *
     * @return int
     */
    public function getAssignId($productId, $sellerId)
    {
        $assignId = 0;
        $collection = $this->getCollection()
                            ->addFieldToFilter('product_id', $productId)
                            ->addFieldToFilter('seller_id', $sellerId);
        foreach ($collection as $item) {
            $assignId = $item->getId();
        }
        return $assignId;
    }

    /**
     * Return Seller Id by Product Id
     *
     * @param int $productId
     *
     * @return int
     */
    public function getSellerIdByProductId($productId)
    {
        $sellerId = 0;
        $collection = $this->getMpProductCollection()
                        ->addFieldToFilter('mageproduct_id', $productId);
        foreach ($collection as $item) {
            $sellerId = $item->getSellerId();
        }
        return $sellerId;
    }

    /**
     * Return Seller Id by Assign Id
     *
     * @param int $assignId
     *
     * @return int
     */
    public function getAssignSellerIdByAssignId($assignId)
    {
        $sellerId = 0;
        $assignProduct = $this->getAssignProduct($assignId);
        if ($assignProduct->getId() > 0) {
            $sellerId = $assignProduct->getSellerId();
        }
        return $sellerId;
    }

    /**
     * Get Product by Assign Id
     *
     * @param int $assignId
     *
     * @return object
     */
    public function getProductByAssignId($assignId)
    {
        $assignData = $this->getAssignDataByAssignId($assignId);
        $product = $this->getProduct($assignData->getProductId());
        return $product;
    }

    /**
     * Get Assign Data by Assign Id
     *
     * @param int $assignId
     *
     * @return object
     */
    public function getAssignDataByAssignId($assignId)
    {
        $assignProduct = $this->getAssignProduct($assignId);
        return $assignProduct;
    }

    /**
     * Check Whether Assign Product is Valid or Not
     *
     * @param int $assignId
     *
     * @return bool
     */
    public function isValidAssignProduct($assignId)
    {
        $customerId = $this->getCustomerId();
        $collection = $this->getCollection()
                            ->addFieldToFilter('id', $assignId)
                            ->addFieldToFilter('seller_id', $customerId);
        foreach ($collection as $item) {
            if ($item->getId() > 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * Update Stock Data of Product
     *
     * @param int $productId
     * @param int $qty
     * @param int $flag [optional]
     * @param int $oldQty [optional]
     */
    public function updateStockData($productId, $qty, $flag = 0, $oldQty = 0)
    {
        try {
            $product = $this->getProduct($productId);
            $stockItem = $this->_stockRegistry->getStockItem($productId);
            if ($flag == 1) {
                $qty = $qty + $stockItem->getQty() - $oldQty;
            } elseif ($flag == 2) {
                $qty = $stockItem->getQty() - $qty;
            } elseif ($flag == 3) {
                $qty = $qty;
            } else {
                $qty = $qty + $stockItem->getQty();
            }
            $stockItem->setData('qty', $qty);
            $stockItem->save();
        } catch (\Exception $e) {
             $e->getMessage();
        }
    }

    /**
     * Update Price of Product
     *
     * @param int $productId
     * @param float $price
     */
    public function updatePrice($productId, $price)
    {
        $product = $this->getProduct($productId);
        $product->addData(['price' => $price]);
        $product->setId($productId)->save();
    }

    /**
     * Get Original Quantity of Product
     *
     * @param int $productId
     *
     * @return int
     */
    public function getOriginalQty($productId, $type = "")
    {
        $totalQty = 0;
        $stockItem = $this->_stockRegistry->getStockItem($productId);
        $totalQty = $stockItem->getQty();
        if ($type == "configurable") {
            $collection = $this->_associates
                                ->create()
                                ->getCollection()
                                ->addFieldToFilter('product_id', $productId);
            foreach ($collection as $associate) {
                $totalQty -= $associate->getQty();
            }
        } else {
            $assignProducts = $this->getAllAssignedProducts($productId);
            foreach ($assignProducts as $assignProduct) {
                $totalQty -= $assignProduct['qty'];
            }
        }
        return $totalQty;
    }

    /**
     * Update Stock Data of Product by Assign Id
     *
     * @param int $assignId
     */
    public function updateStockDataByAssignId($assignId)
    {
        $assignData = $this->getAssignDataByAssignId($assignId);
        $productId = $assignData->getProductId();
        $qty = $assignData->getQty();
        $this->updateStockData($productId, $qty, 2);
    }

    /**
     * Update Stock Data of Product by Assign Id
     *
     * @param int $assignId
     */
    public function updateConfigStockDataByAssignId($assignId)
    {
        $model = $this->_associates->create();
        $collection = $model->getCollection()->addFieldToFilter("parent_id", $assignId);
        foreach ($collection as $key => $item) {
            $this->updateStockData($item->getProductId(), $item->getQty(), 2);
            $this->deleteItem($item);
        }
    }

    public function deleteItem($item)
    {
        $item->delete();
    }

    /**
     * Update Assign Product Quote by Assign Id
     *
     * @param int $assignId
     */
    public function updateQuote($assignId)
    {
        $itemIds = [];
        $collection = $this->getQuoteCollection()
                            ->addFieldToFilter('assign_id', $assignId);
        foreach ($collection as $item) {
            $itemIds[] = $item->getItemId();
            $this->deleteItem($item);
        }
        $this->updateCart($itemIds);
    }

    /**
     * Update Cart
     *
     * @param int|array $itemIds
     */
    public function updateCart($itemIds)
    {
        $cartModel = $this->getCart();
        $quote = $cartModel->getQuote();
        foreach ($quote->getAllVisibleItems() as $item) {
            $id = $item->getId();
            if (in_array($id, $itemIds)) {
                $cartModel = $this->removeCartItem($cartModel, $id);
            }
        }
        $cartModel->save();
    }

    /**
     * Remove itme form cart
     *
     * @param object $cartModel
     * @param int $id
     * @return object
     */
    public function removeCartItem($cartModel, $id)
    {
        $cartModel->removeItem($id)->save();
        return $cartModel;
    }

    /**
     * Check Product Quantities are Available from Seller on Cart
     */
    public function checkStatus()
    {
        $result = [];
        $updateRequired = false;
        $cartModel = $this->getCart();
        $quote = $cartModel->getQuote();
        foreach ($quote->getAllItems() as $item) {
            $deleteRequire = false;
            $product = $item->getProduct();
            $productId = $item->getProductId();
            $productType = $product->getTypeId();
            if ($productType == "configurable") {
                continue;
            }
            $allowedProductTypes = $this->getAllowedProductTypes();
            if (in_array($productType, $allowedProductTypes)) {
                $item = $item->getParentItem() ? $item->getParentItem() : $item;
                $product = $item->getProduct();
                $parentProductId = $item->getProductId();
                $productType = $product->getTypeId();
                $itemId = $item->getId();
                $requestedQty = $item->getQty();
                $assignData = $this->getAssignDataByItemId($itemId);
                if ($assignData['assign_id'] > 0) {
                    $assignId = $assignData['assign_id'];
                    if ($this->isEnabled($assignId)) {
                        if ($assignData['child_assign_id'] > 0) {
                            $childAssignId = $assignData['child_assign_id'];
                            $qty = $this->getAssociteQty($assignId, $childAssignId);
                        } else {
                            $assignData = $this->getAssignDataByAssignId($assignId);
                            $qty = $assignData->getQty();
                        }
                    } else {
                        $deleteRequire = true;
                        $this->_messageManager
                        ->addError(__('Product is currently not available from seller.'));
                    }
                } else {
                    $qty = $this->getOriginalQty($productId, $productType, $parentProductId);
                }
                if ($requestedQty > $qty) {
                    $updateRequired = true;
                    $item->setQty($qty);
                    $this->_messageManager
                        ->addError(__('Quantities are not available from seller.'));
                }
                $result[] = $qty;
                if ($qty <= 0 || $deleteRequire) {
                    $cartModel = $this->removeCartItem($cartModel, $itemId);
                }
            }
        }
        if ($updateRequired) {
            $cartModel->save();
        }
    }

    /**
     * Set Updated Price of Product
     */
    public function checkCartPrice()
    {
        $cartModel = $this->getCart();
        $quote = $cartModel->getQuote();
        $this->collectTotals($quote);
        $cartModel->getQuote()->collectTotals()->save();
    }

    /**
     * Get Seller Details by Seller Id
     *
     * @param int $sellerId
     *
     * @return object
     */
    public function getSellerDetails($sellerId)
    {
        $seller = false;
        $collection = $this->_sellerCollection
                            ->create()
                            ->addFieldToFilter('seller_id', ['eq' => $sellerId])
                            ->addFieldToFilter('store_id', ['eq' => $this->getStore()->getId()]);
        if (!$collection->getSize()) {
            $collection = $this->_sellerCollection
                            ->create()
                            ->addFieldToFilter('seller_id', ['eq' => $sellerId])
                            ->addFieldToFilter('store_id', ['eq' => 0]);
        }

        foreach ($collection as $seller) {
            return $seller;
        }
        return $seller;
    }

    /**
     * Assign Product to Seller
     *
     * @param array $data
     * @param int $flag [optional]
     *
     * @return array
     */
    public function assignProduct($data, $flag = 0)
    {
        $result = [
                    'assign_id' => 0,
                    'product_id' => 0,
                    'error' => 0,
                    'msg' => '',
                    'qty' => 0,
                    'flag' => 0,
                    'status' => 1,
                    'type' => 0
                ];
        $productId = (int) $data['product_id'];
        $condition = (int) $data['product_condition'];
        $assignProductId = (int) $data['assign_product_id'];
        $qty = (int) $data['qty'];
        $price = (float) $data['price'];
        $description = $data['description'];
        $image = $data['image'];
        $ownerId = $this->getSellerIdByProductId($productId);
        $sellerId = $this->getCustomerId();
        $product = $this->getProduct($productId);
        $type = $product->getTypeId();
        $date = date('Y-m-d');
        $result['condition'] = $condition;
        if ($qty < 0) {
            $qty = 0;
        }
        $shippingCountryCharge = $data['shipping_country_charge'] ?? '';
        $assignProductData = [
            'product_id' => $productId,
            'owner_id' => $ownerId,
            'seller_id' => $sellerId,
            'qty' => $qty,
            'price' => $price,
            'description' => $description,
            'condition' => $condition,
            'type' => $type,
            'created_at' => $date,
            'image' => $image,
            'status' => 1,
            'shipping_country_charge' => $shippingCountryCharge,
            'assign_product_id' => $assignProductId
        ];
        if ($image == '') {
            unset($assignProductData['image']);
        }
        if ($data['del'] == 1) {
            $assignProductData['image'] = "";
        }
        $model = $this->_items->create();
        $currentStoreId = $this->getStoreId();
        if ($flag == 1) {
            $assignId = $data['assign_id'];
            $assignData = $this->getAssignDataByAssignId($assignId);
            $oldPrice = $assignData->getPrice();
            if ($assignData->getId() > 0) {
                $oldImage = $assignData->getImage();
                if ($oldImage != $image && $image != "") {
                    $assignProductData['image'] = $image;
                }
                $oldQty = $assignData->getQty();
                $status = $assignData->getStatus();
                $result['old_qty'] = $oldQty;
                $result['prev_status'] = $status;
                $result['flag'] = 1;
                unset($assignProductData['created_at']);
                if ($this->isEditApprovalRequired()) {
                    $result['status'] = 0;
                    $assignProductData['status'] = 0;
                    $attributData = [
                      'status' => \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED
                    ];
                    $this->updateProductData([$assignProductId], $attributData, $currentStoreId);
                }
            } else {
                return $result;
            }
            $model->addData($assignProductData)->setId($assignId)->save();
            //$this->manageDescription($data, $assignId);
        } else {
            if ($this->isAddApprovalRequired()) {
                $result['status'] = 0;
                $assignProductData['status'] = 0;
                $attributData = [
                  'status' => \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED
                ];
                $this->updateProductData([$assignProductId], $attributData, $currentStoreId);
            }
            $model->setData($assignProductData)->save();
            //$this->manageDescription($data, $model->getId());
        }
        if ($model->getId() > 0) {
            $result['product_id'] = $productId;
            $result['qty'] = $qty;
            $result['assign_id'] = $model->getId();
        }
        return $result;
    }

    /**
     * Process Assigned Product
     *
     * @param array $data
     * @param int $type
     * @param int $flag
     * @return array
     */
    public function processAssignProduct($data, $type, $flag)
    {
        if ($type == "configurable") {
            return $this->assignConfigProduct($data, $flag);
        } else {
            return $this->assignProduct($data, $flag);
        }
    }

    /**
     * Assign Product to Seller
     *
     * @param array $data
     * @param int $flag [optional]
     *
     * @return array
     */
    public function assignConfigProduct($data, $flag = 0)
    {
        $result = [
                    'assign_id' => 0,
                    'product_id' => 0,
                    'error' => 0,
                    'msg' => '',
                    'qty' => 0,
                    'flag' => 0,
                    'status' => 1,
                    'type' => 1,
                    'associates' => ''
                ];
        $productId = (int) $data['product_id'];
        $condition = (int) $data['product_condition'];
        $assignProductId = (int) $data['assign_product_id'];
        $description = $data['description'];
        $image = $data['image'];
        $ownerId = $this->getSellerIdByProductId($productId);
        $sellerId = $this->getCustomerId();
        $type = "configurable";
        $date = date('Y-m-d');
        $qty = 0;
        $price = 0;
        $result['condition'] = $condition;
        $shippingCountryCharge = $data['shipping_country_charge'] ?? '';
        $assignProductData = [
            'product_id' => $productId,
            'owner_id' => $ownerId,
            'seller_id' => $sellerId,
            'qty' => 0,
            'price' => 0,
            'description' => $description,
            'condition' => $condition,
            'type' => $type,
            'created_at' => $date,
            'image' => $image,
            'status' => 1,
            'shipping_country_charge' => $shippingCountryCharge,
            'assign_product_id' => $assignProductId
        ];
        if ($image == '') {
            unset($assignProductData['image']);
        }
        if ($data['del'] == 1) {
            $assignProductData['image'] = "";
        }
        $model = $this->_items->create();
        if ($flag == 1) {
            $assignId = $data['assign_id'];
            $assignData = $this->getAssignDataByAssignId($assignId);
            if ($assignData->getId() > 0) {
                $oldImage = $assignData->getImage();
                $status = $assignData->getStatus();
                $result['prev_status'] = $status;
                if ($oldImage != $image && $image != "") {
                    $assignProductData['image'] = $image;
                }
                $result['flag'] = 1;
                unset($assignProductData['created_at']);
                if ($this->isEditApprovalRequired() || $this->isAddApprovalRequired()) {
                    $result['status'] = 0;
                    $assignProductData['status'] = 0;
                    $attributData = [
                      'status' => \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED
                    ];
                    $this->updateProductData([$assignProductId], $attributData, 0);
                }
            } else {
                return $result;
            }
            $model->addData($assignProductData)->setId($assignId)->save();
          //  $this->manageDescription($data, $assignId);
        } else {
            if ($this->isAddApprovalRequired()) {
                $result['status'] = 0;
                $assignProductData['status'] = 0;
                $attributData = [
                  'status' => \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED
                ];
                $this->updateProductData([$assignProductId], $attributData, 0);
            }
            $model->setData($assignProductData)->save();
          //  $this->manageDescription($data, $model->getId());
        }
        if ($model->getId() > 0) {
            $result['product_id'] = $productId;
            $result['qty'] = $qty;
            $result['assign_id'] = $model->getId();
            // set associated options
            $result['associates'] = $this->setAssociatedOptions($data, $model->getId(), $productId);
        }
        return $result;
    }

    /**
     * Set Associated Product Data
     *
     * @param array $data
     * @param int $parentId
     * @param int $parentProductId
     * @return array
     */
    public function setAssociatedOptions($data, $parentId, $parentProductId)
    {
        if ($this->isAddApprovalRequired()) {
            $status = 0;
        } else {
            $status = 1;
        }
        $result = [];
        foreach ($data['products'] as $productId => $info) {
            if (array_key_exists("id", $info)) {
                $options = "";
                $qty = $info['qty'];
                $price = $info['price'];
                $assignProductId = $info['new_product_id'];
                $item = [];
                $item['product_id'] = $productId;
                $item['parent_id'] = $parentId;
                $item['parent_product_id'] = $parentProductId;
                $item['assign_product_id'] = $assignProductId;
                $item['options'] = $options;
                $item['qty'] = $qty;
                $item['price'] = $price;
                if (array_key_exists("associate_id", $info)) { //edit product case
                    $associate = $this->getAssociatedItem($info['associate_id']);
                    $oldPrice = $associate->getPrice();
                    $oldQty = $associate->getQty();
                    $result[$productId] = ['qty' => $qty, 'old_qty' => $oldQty, 'manage_stock' => true];
                    $this->updateRecord($associate, $item, $info['associate_id']);
                } else {
                    $result[$productId] = ['qty' => $qty, 'manage_stock' => false];
                    $this->newRecord($this->_associates->create(), $item);
                }
            }
        }
        return $result;
    }

    /**
     * Update Model
     *
     * @param object $model
     * @param array $data
     * @param int $id
     * @return void
     */
    public function updateRecord($model, $data, $id)
    {
        return $model->addData($data)->setId($id)->save();
    }

    /**
     * Save New Record
     *
     * @param object $model
     * @param array $data
     * @return void
     */
    public function newRecord($model, $data)
    {
        return $model->setData($data)->save();
    }

    /**
     * Fetch Associated Product
     *
     * @param int $associateId
     * @return void
     */
    public function getAssociatedItem($associateId)
    {
        return $this->_associates->create()->load($associateId);
    }

    /**
     * Dissapprove Assign Product
     *
     * @param int $assignId
     * @param int $status [optional]
     * @param int $flag [optional]
     * @param int $qty [optional]
     */
    public function disApproveProduct($assignId, $status = 0, $flag = 0, $qty = 0)
    {
        $assignProduct = $this->getAssignProduct($assignId);
        if ($assignProduct->getId() > 0) {
            if ($status == 1) {
                $productId = $assignProduct->getProductId();
                $data = [];
                $data['status'] = 0;
                $data['seller_id'] = $assignProduct->getSellerId();
                $data['product_id'] = $assignProduct->getProductId();
                $data['qty'] = $assignProduct->getQty();
                $assignProduct->setData($data)->setId($assignId)->save();
                if ($flag == 1) {
                    $qty = $assignProduct->getQty();
                }
                $this->updateStockData($productId, $qty, 2);
            }
        }
        return $assignProduct;
    }

    /**
     * Dissapprove Assign Product
     *
     * @param int $assignId
     * @param int $status [optional]
     * @param int $flag [optional]
     * @param int $qty [optional]
     */
    public function disApproveConfigProduct($data, $status = 0, $byAdmin = false)
    {
        if (!$byAdmin) {
            $assignId = $data['assign_id'];
        } else {
            $assignId = $data;
        }
        $assignProduct = $this->getAssignProduct($assignId);
        if ($assignProduct->getId() > 0) {
            if ($status == 1) {
                $parentProductId = $assignProduct->getProductId();
                $prodata = [];
                $prodata['status'] = 0;
                $prodata['seller_id'] = $assignProduct->getSellerId();
                $prodata['product_id'] = $assignProduct->getProductId();
                $assignProduct->setData($prodata)->setId($assignId)->save();
                if ($byAdmin) {
                    $model = $this->_associates->create();
                    $collection = $model->getCollection()->addFieldToFilter("parent_id", $assignId);
                    foreach ($collection as $key => $item) {
                        $this->updateStockData($item->getProductId(), $item->getQty(), 2);
                    }
                } else {
                    foreach ($data['associates'] as $productId => $info) {
                        if ($info['manage_stock']) {
                            $this->updateStockData($productId, $info['qty'], 2);
                        }
                    }
                }
            }
        }
        return $assignProduct;
    }

    /**
     * Approve Assign Product
     *
     * @param int $assignId
     */
    public function approveProduct($assignId)
    {
        $assignProduct = $this->getAssignProduct($assignId);
        if ($assignProduct->getId() > 0) {
            $status = $assignProduct->getStatus();
            if ($status == 0) {
                $productId = $assignProduct->getProductId();
                $qty = $assignProduct->getQty();
                $data = [];
                $data['status'] = 1;
                $data['seller_id'] = $assignProduct->getSellerId();
                $data['product_id'] = $assignProduct->getProductId();
                $assignProduct->setData($data)->setId($assignId)->save();
                $this->updateStockData($productId, $qty);
            }
        }
        return $assignProduct;
    }

    /**
     * Approve Assign Product
     *
     * @param int $assignId
     */
    public function approveConfigProduct($assignId)
    {
        $assignProduct = $this->getAssignProduct($assignId);
        if ($assignProduct->getId() > 0) {
            $status = $assignProduct->getStatus();
            if ($status == 0) {
                $productId = $assignProduct->getProductId();
                $qty = $assignProduct->getQty();
                $data = [];
                $data['status'] = 1;
                $data['seller_id'] = $assignProduct->getSellerId();
                $data['product_id'] = $assignProduct->getProductId();
                $assignProduct->setData($data)->setId($assignId)->save();
                $model = $this->_associates->create();
                $collection = $model->getCollection()->addFieldToFilter("parent_id", $assignId);
                foreach ($collection as $key => $item) {
                    $this->updateStockData($item->getProductId(), $item->getQty());
                }
            }
        }
        return $assignProduct;
    }

    /**
     * Get Assign Products
     *
     * @param int $productId
     * @param string $sort [optional]
     * @param string $order [optional]
     *
     * @return collection object
     */
    public function getAssignProducts($productId, $sort = '', $order = 'ASC', $exclude = false)
    {
        $collection = $this->getCollection();
        $websiteId = $this->_storeManager->getWebsite()->getId();
        $proPriceAttrId = $this->eavAttribute->getIdByCode("catalog_product", "price");
        $catalogProductEntityDecimal = $this->_resource->getTableName('catalog_product_entity_decimal');
        $catalogInventoryStockItem = $this->_resource->getTableName('cataloginventory_stock_item');
        $collection->getSelect()->joinLeft(
            $catalogProductEntityDecimal.' as cped',
            'main_table.assign_product_id = cped.entity_id and cped.store_id = 0
            AND cped.attribute_id = '.$proPriceAttrId,
            ["product_price" => "value"]
        );
        $collection->getSelect()->join(
            $catalogInventoryStockItem.' as csi',
            'main_table.assign_product_id = csi.product_id',
            ["assign_qty" => "qty"]
        )->where("csi.website_id = 0 OR csi.website_id = ".$websiteId);
        $joinTable = $this->_resource
                        ->getTableName('marketplace_userdata');
        $sql = 'mp.seller_id = main_table.seller_id';
        $sql .= ' and main_table.product_id = '.$productId;
        $sql .= ' and mp.is_seller = 1';
        $sql .= ' and mp.store_id = 0';
        if ($exclude) {
            $sql .= ' and csi.qty > 0';
        }
        $sql .= ' and main_table.status = 1';
        $fields = ['seller_id', 'is_seller', 'shop_url', 'shop_title'];
        $collection->getSelect()->join($joinTable.' as mp', $sql, $fields);
        $collection->getSelect()->group('main_table.seller_id');
        $collection->addFilterToMap('seller_id', 'main_table.seller_id');
        $collection->addFilterToMap('status', 'main_table.status');

        if ($sort != '') {
            $collection->setOrder($sort, $order);
        }
        return $collection;
    }

    /**
     * Get All Product Details Including Assign Products
     *
     * @param int $productId
     * @param int $mode [optional]
     * @param string $sort [optional]
     * @param string $order [optional]
     *
     * @return array
     */
    public function getTotalProducts($productId, $mode = 0, $sort = '', $order = 'ASC')
    {
        $totalProducts = [];
        $collection = $this->getAssignProducts($productId, $sort, $order);
        foreach ($collection as $assignProduct) {
            $productData = [];
            $productData['id'] = $assignProduct->getId();
            $productData['description'] = $assignProduct->getDescription();
            $productData['price'] = (float) $assignProduct->getProductPrice();
            $productData['qty'] = (int) $assignProduct->getAssignQty();
            $productData['assign_id'] = $assignProduct->getId();
            $productData['seller_id'] = $assignProduct->getSellerId();
            $productData['image'] = $assignProduct->getImage();
            $productData['condition'] = $assignProduct->getCondition();
            $totalProducts[] = $productData;

            if ($mode == 0) {
                $product = $this->getProduct($productId);
                if ($product->getId()) {
                    $sellerId = $this->getSellerIdByProductId($productId);
                    $productData = [];
                    $productData['id'] = $assignProduct->getId();
                    $productData['description'] = $product->getDescription();
                    $productData['price'] = (float) $product->getFinalPrice();
                    $productData['qty'] = (int) $product->getQty();
                    $productData['assign_id'] = 0;
                    $productData['seller_id'] = $sellerId;
                    $totalProducts[] = $productData;
                }
            }
        }
        return $totalProducts;
    }

    /**
     * Get All Assign Product Details Excluding Main Product
     *
     * @param int $productId
     *
     * @return array
     */
    public function getAllAssignedProducts($productId)
    {
        $totalProducts = $this->getTotalProducts($productId, 1);
        return $totalProducts;
    }

    /**
     * Get Minimum Price with Currency
     *
     * @param int $productId
     * @param string $type
     * @param boolean $sellerId
     * @param boolean $listPage
     * @return string
     */
    public function getMinimumPriceHtml($productId, $type = '-', $sellerId = false, $listPage = false)
    {
        $prices = [];
        $sellerIds = [];
        if ($type == "configurable") {
            $model = $this->_associates->create();
            $collection = $model->getCollection()->addFieldToFilter("parent_product_id", $productId);
            if ($sellerId) {
                return $collection;
            } else {
                foreach ($collection as $key => $item) {
                    $prices[$key] = $item->getPrice();
                }
            }
        } else {
            $totalProducts = $this->getTotalProducts($productId);
            if ($sellerId) {
                return $totalProducts;
            } else {
                foreach ($totalProducts as $key => $product) {
                    $prices[$key] = $product['price'];
                }
            }
        }
        sort($prices);
        $price = $prices[0];
        if ($listPage) {
            return $price;
        }
        return $this->_currency->currency($price, true, false);
    }

    /**
     * Check Whether Product is Assigned to Seller or Not
     *
     * @param int $productId
     *
     * @return bool
     */
    public function productHasSeller($productId)
    {
        $flag = 0;
        $collection = $this->getMpProductCollection()
                            ->addFieldToFilter('mageproduct_id', $productId);
        foreach ($collection as $sellerProduct) {
            if ($sellerProduct->getId()) {
                $flag = 1;
            }
        }
        if ($flag == 1) {
            return true;
        }
        return false;
    }

    /**
     * Get Sorting Order Info
     *
     * @return array
     */
    public function getSortingOrderInfo()
    {
        $assignType = $this->getAssignType();
        if ($assignType == 1) {
            $result = ['sort_by' => 'price', 'order_type' => 'DESC'];
        } elseif ($assignType == 2) {
            $result = ['sort_by' => 'qty', 'order_type' => 'ASC'];
        } elseif ($assignType == 3) {
            $result = ['sort_by' => 'qty', 'order_type' => 'DESC'];
        } else {
            $result = ['sort_by' => 'price', 'order_type' => 'ASC'];
        }
        return $result;
    }

    /**
     * Assign Product to Seller By Product Id
     *
     * @param int $productId
     */
    public function assignSeller($productId)
    {
        if ($this->hasAssignedProducts($productId)) {
            $price = 0;
            $totalQty = 0;
            $assignId = 0;
            $sellerId = 0;
            $sortingInfo = $this->getSortingOrderInfo();
            $sortBy = $sortingInfo['sort_by'];
            $orderType = $sortingInfo['order_type'];
            $assignProducts = $this->getTotalProducts($productId, 1, $sortBy, $orderType);
            foreach ($assignProducts as $key => $product) {
                $proId = $product['id'];
                $assignId = $product['assign_id'];
                $sellerId = $product['seller_id'];
                $price = $product['price'];
                $totalQty = $product['qty'];
                break;
            }
            $associatedPro = $this->getAssignedAssociatedProduct($productId, $proId);
            $associateProducts = $associatedPro->getData();
            if ($associateProducts) {
                foreach ($associateProducts as $key => $product) {
                    $this->updateStockData($product['product_id'], $product['qty'], 3);
                    $this->updatePrice($product['product_id'], $product['price']);
                    $collection = $this->getMpProductCollection();
                    $sellerProduct = $this->getDataByField($product['product_id'], 'mageproduct_id', $collection);
                    if ($sellerProduct) {
                        if ($sellerId > 0) {
                            $sellerProduct->addData(['seller_id' => $sellerId])
                                        ->setId($sellerProduct->getId())
                                        ->save();
                        }
                    }
                }
            }
            $this->updateStockData($productId, $totalQty, 3);
            $this->updatePrice($productId, $price);
            $collection = $this->getMpProductCollection();
            $sellerProduct = $this->getDataByField($productId, 'mageproduct_id', $collection);
            if ($sellerProduct) {
                if ($sellerId > 0) {
                    $sellerProduct->addData(['seller_id' => $sellerId])
                                ->setId($sellerProduct->getId())
                                ->save();
                }
            }
            $assignProduct = $this->getAssignProduct($assignId);
            $product = $this->productRepository->getById($assignProduct->getAssignProductId());
            if ($assignProduct->getType() == "configurable") {
                $this->deleteAssociatedProducts($assignProduct->getId());
            }
            $this->productRepository->delete($product);
            if ($sellerId > 0) {
                $collection = $this->getCollection()->addFieldToFilter('product_id', $productId);
                foreach ($collection as $assignProduct) {
                    $this->updateAssignProductOwner($assignProduct, $sellerId);
                }
            }
        }
    }

    /**
     * Update the assigned product other seller, in case of product deleted by owner
     *
     * @param object $assignProduct
     * @param int $sellerId
     */
    public function updateAssignProductOwner($assignProduct, $sellerId)
    {
        $assignProduct->addData(['owner_id' => $sellerId])
                    ->setId($assignProduct->getId())
                    ->save();
    }

    /**
     * Remove All Pending Assign Product If Main Product Does Not Exist
     *
     * @param int $productId
     */
    public function removeAssignProducts($productId)
    {
        $product = $this->getProduct($productId);
        if (!$product->getId()) {
            $assignId = 0;
            $collection = $this->getCollection()
                                ->addFieldToFilter('product_id', $productId);
            foreach ($collection as $item) {
                $this->removeAssignProduct($item);
            }
        }
    }

    /**
     * Remove Assign Product If Main Product Does Not Exist
     *
     * @param int $productId
     */
    public function removeAssignProduct($product)
    {
        $product->delete();
    }

    /**
     * Check Whether Product Has Assigned Product Or Not
     *
     * @param int $productId [optional]
     *
     * @return bool
     */
    public function hasAssignedProducts($productId = 0)
    {
        $assignProductCollection = $this->getAssignProducts($productId);
        if ($assignProductCollection->getSize()) {
            return true;
        }

        return false;
    }

    /**
     * Check Whether Added Product to Cart is New or Not
     *
     * @return bool
     */
    public function isNewProduct($productId = 0, $assignId = 0, $childAssignId = 0)
    {
        if ($productId == 0) {
            $productId = (int) $this->_request->getParam('product');
        }
        if ($assignId == 0) {
            $assignId = (int) $this->_request->getParam('mpassignproduct_id');
        }
        if ($childAssignId == 0) {
            $childAssignId = (int) $this->_request->getParam('associate_id');
        }
        $cartModel = $this->getCart();
        $quoteId = $cartModel->getQuote()->getId();
        $collection = $this->getQuoteCollection()
                            ->addFieldToFilter('product_id', $productId)
                            ->addFieldToFilter('assign_id', $assignId)
                            ->addFieldToFilter('child_assign_id', $childAssignId)
                            ->addFieldToFilter('quote_id', $quoteId);
        foreach ($collection as $item) {
            if ($item->getId() > 0) {
                return false;
            }
        }
        return true;
    }

    /**
     * Get Quote Item Id to Add Quantity to Existing Item In Cart According to Seller
     *
     * @param int $assignId
     * @param int $productId
     * @param int $quoteId
     *
     * @return int
     */
    public function getRequestedItemId($assignId, $productId, $quoteId)
    {
        $itemId = 0;
        $collection = $this->getQuoteCollection()
                            ->addFieldToFilter('assign_id', $assignId)
                            ->addFieldToFilter('product_id', $productId)
                            ->addFieldToFilter('quote_id', $quoteId);
        foreach ($collection as $item) {
            $itemId = $item->getItemId();
            break;
        }
        return $itemId;
    }

    /**
     * Get Price of Assign Product by Assign Id
     *
     * @param int $assignId
     *
     * @return float
     */
    public function getAssignProductPrice($assignId)
    {
        $price = 0;
        $assignProduct = $this->getAssignProduct($assignId);
        if ($assignProduct->getId() > 0) {
            $price = $assignProduct->getPrice();
        }
        return $price;
    }

    /**
     * Get Assign Data by Quote Item Id
     *
     * @param int $itemId
     *
     * @return array
     */
    public function getAssignDataByItemId($itemId)
    {
        $assignData = ['assign_id' => 0];
        $collection = $this->getQuoteCollection()
                            ->addFieldToFilter('item_id', $itemId);
        foreach ($collection as $item) {
            $assignData['seller_id'] = $item->getSellerId();
            $assignData['assign_id'] = $item->getAssignId();
            $assignData['child_assign_id'] = $item->getChildAssignId();
            break;
        }
        return $assignData;
    }

    /**
     * Check Whether Quantity is Allowed from Seller or Not
     *
     * @param int $qty
     * @param int $productId
     * @param int $assignId
     *
     * @return bool
     */
    public function isQtyAllowed($qty, $productId, $assignId)
    {
        $product = $this->getProduct($productId);
        $productType = $product->getTypeId();
        $allowedProductTypes = $this->getAllowedProductTypes();
        if (!in_array($productType, $allowedProductTypes)) {
            return true;
        }
        $totalQty = 0;
        if ($assignId == 0) {
            $stockItem = $this->_stockRegistry->getStockItem($productId);
            $totalQty = $stockItem->getQty();
            $collection = $this->getCollection()
                            ->addFieldToFilter('product_id', $productId)
                            ->addFieldToFilter('status', 1);
            foreach ($collection as $item) {
                $totalQty = $totalQty - (int) $item->getQty();
            }
        } else {
            $assignProduct = $this->getAssignProduct($assignId);
            if ($assignProduct->getId() > 0) {
                $totalQty = (int) $assignProduct->getQty();
            }
        }
        $inCartQty = $this->inCartQty($productId, $assignId);
        $totalQty = $totalQty - $inCartQty;
        if ($totalQty >= $qty) {
            return true;
        }
        return false;
    }

    /**
     * Fetch data from Array
     *
     * @param array $array
     * @param string $key
     * @param integer $defaultValue
     * @return $value
     */
    public function getValueFromArray($array, $key, $defaultValue = 0)
    {
        $value = $defaultValue;
        if (array_key_exists($key, $array)) {
            $value = $array[$key];
        }
        return $value;
    }

    /**
     * Check Whether Quantity is Allowed from Seller or Not
     *
     * @param int $qty
     * @param int $productId
     * @param int $assignId
     *
     * @return bool
     */
    public function isConfigQtyAllowed($info, $product)
    {
        $totalQty = 0;
        $productType = $product->getTypeId();
        $allowedProductTypes = $this->getAllowedProductTypes();
        if (!in_array($productType, $allowedProductTypes)) {
            return true;
        }
        $assignId = 0;
        $childAssignId = 0;
        $qty = 1;
        $productId = $product->getId();
        if (is_array($info)) {
            $assignId = $this->getValueFromArray($info, 'mpassignproduct_id');
            $childAssignId = $this->getValueFromArray($info, 'associate_id');
            $qty = $this->getValueFromArray($info, 'qty', 1);
        }
        if ($assignId == 0) {
            if (!$this->hasAssignedProducts($productId)) {
                return true;
            }
            $childProductId = 0;
            if (array_key_exists("selected_configurable_option", $info)) {
                $childProductId = $info['selected_configurable_option'];
                $totalQty = $this->getTotalQty($childProductId, $productId);
            }
        } else {
            try {
                $assignProduct = $this->_associates->create()->load($childAssignId);
                if ($assignProduct->getId() > 0) {
                    $totalQty = (int) $assignProduct->getQty();
                }
            } catch (\Exception $e) {
                return false;
            }
        }
        $inCartQty = $this->inCartQty($productId, $assignId, $childAssignId);
        $totalQty = $totalQty - $inCartQty;
        if ($totalQty >= $qty) {
            return true;
        }
        return true;
    }

    /**
     * Get Child Product Quantity
     *
     * @param int $childProductId
     * @param int $productId
     * @return void
     */
    public function getTotalQty($childProductId, $productId)
    {
        $stockItem = $this->_stockRegistry->getStockItem($childProductId);
        $totalQty = $stockItem->getQty();
        $collecton = $this->_itemsCollection
                        ->create()
                        ->addFieldToFilter("product_id", $productId)
                        ->addFieldToFilter("status", 1);
        if ($collecton->getSize()) {
            $allIds = [];
            foreach ($collecton as $item) {
                $allIds[] = $item->getId();
            }
            $collection = $this->_associates
                        ->create()
                        ->getCollection()
                        ->addFieldToFilter('parent_id', ["in" => $allIds])
                        ->addFieldToFilter('product_id', $childProductId);
        } else {
            $collection = $this->_associates
                        ->create()
                        ->getCollection()
                        ->addFieldToFilter('product_id', $childProductId);
        }
        foreach ($collection as $item) {
            $totalQty = $totalQty - (int) $item->getQty();
        }
        return $totalQty;
    }

    /**
     * Get Quantity Present in Cart
     *
     * @param int $productId
     * @param int $assignId
     *
     * @return int
     */
    public function inCartQty($productId, $assignId, $childAssignId = 0)
    {
        $qty = 0;
        $cartModel = $this->getCart();
        $quoteId = $cartModel->getQuote()->getId();
        if ($childAssignId > 0) {
            $collection = $this->getQuoteCollection()
                            ->addFieldToFilter('product_id', $productId)
                            ->addFieldToFilter('assign_id', $assignId)
                            ->addFieldToFilter('child_assign_id', $childAssignId)
                            ->addFieldToFilter('quote_id', $quoteId);
        } else {
            $collection = $this->getQuoteCollection()
                            ->addFieldToFilter('product_id', $productId)
                            ->addFieldToFilter('assign_id', $assignId)
                            ->addFieldToFilter('quote_id', $quoteId);
        }

        foreach ($collection as $item) {
            $qty = $item->getQty();
        }
        return $qty;
    }

    /**
     * Get Assign Product Total Quantity by Product Id
     *
     * @param int $productId
     *
     * @return int
     */
    public function getAssignProductQty($productId, $check = false)
    {
        $totalQty = 0;
        $assignProducts = $this->getAllAssignedProducts($productId);
        if (count($assignProducts) == 0) {
            if ($check) {
                $parentId = $this->_configurableCollection->getParentIdsByChild($productId);
                if (count($parentId)) {
                    return $this->getAssocatedProductQty($productId);
                }
            }
        }
        foreach ($assignProducts as $assignProduct) {
            $totalQty += $assignProduct['qty'];
        }

        return $totalQty;
    }

    /**
     * Get Full Action Name
     *
     * @return string
     */
    public function getFullActionName()
    {
        return $this->_request->getFullActionName();
    }

    /**
     * Check Whether Customer Is Seller Or Not
     *
     * @param int $sellerId [Optional]
     *
     * @return bool
     */
    public function isSeller($sellerId = '')
    {
        if ($sellerId == '') {
            $sellerId = $this->getCustomerId();
        }
        $seller = $this->getSellerDetails($sellerId);
        if (!is_object($seller)) {
            return false;
        }
        $isSeller = $seller->getIsSeller();
        if ($isSeller == 1) {
            return true;
        }
        return false;
    }

    /**
     * Get Image Url for Assign Product
     *
     * @param string $image
     *
     * @return string
     */
    public function getImageUrl($image)
    {
        $currentStore = $this->_storeManager->getStore();
        $mediaUrl = $currentStore->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
        $imageUrl = $mediaUrl.'marketplace/assignproduct/product/'.$image;
        return $imageUrl;
    }

    /**
     * Get First Object From Collection
     *
     * @param array | int | string $value
     * @param array | string $field
     * @param object $collection
     *
     * @return $object
     */
    public function getDataByField($values, $fields, $collection)
    {
        $item = false;
        if (is_array($values)) {
            foreach ($values as $key => $value) {
                $field = $fields[$key];
                $collection = $collection->addFieldToFilter($field, $value);
            }
        } else {
            $collection = $collection->addFieldToFilter($fields, $values);
        }
        foreach ($collection as $item) {
            return $item;
        }
        return $item;
    }

    /**
     * Update Stock Quantity
     *
     * @param array $result
     */
    public function manageProductStock($result)
    {
        if ($result['status'] == 1) {
            if ($result['type'] == 1) {
                foreach ($result['associates'] as $productId => $info) {
                    $this->updateStockData($productId, $info['qty']);
                }
            } else {
                $this->updateStockData($result['product_id'], $result['qty']);
            }
        }
    }

    /**
     * Manage Product Stock and Status
     *
     * @param array $result
     */
    public function manageProductStockAndStatus($result)
    {
        if ($result['status'] == 1) {
            if ($result['type'] == 1) {
                foreach ($result['associates'] as $productId => $info) {
                    if ($info['manage_stock']) {
                        $this->updateStockData($productId, $info['qty'], 1, $info['old_qty']);
                    } else {
                        $this->updateStockData($productId, $info['qty']);
                    }
                }
            } else {
                $this->updateStockData($result['product_id'], $result['qty'], 1, $result['old_qty']);
            }
        } else {
            if ($result['type'] == 1) {
                $this->disApproveConfigProduct($result, $result['prev_status']);
            } else {
                $this->disApproveProduct($result['assign_id'], $result['prev_status'], 0, $result['old_qty']);
            }
        }
    }

    /**
     * Manage Product Stock Data
     *
     * @param array $result
     */
    public function processProductStatus($result)
    {
        if ($result['flag'] == 1) {
            if ($result['status'] == 0) {
                $this->sendProductMail($result, true);
            }
        } else {
            if ($result['status'] == 0) {
                $this->sendProductMail($result);
            }
        }
    }

    /**
     * Remove Unnecessasy Assocaited Assigned Products
     *
     * @param array $result
     */
    public function processConfigProduct($result)
    {
        $productIds = [];
        $assignId = $result['assign_id'];
        foreach ($result['associates'] as $productId => $info) {
            $productIds[] = $productId;
        }
        $model = $this->_associates->create();
        $collection = $model->getCollection()->addFieldToFilter("parent_id", $assignId);
        foreach ($collection as $key => $item) {
            if (!in_array($item->getProductId(), $productIds)) {
                $this->updateStockData($item->getProductId(), $item->getQty(), 2);
                $this->deleteItem($item);
            }
        }
    }

    /**
     * Manage Assigned Products Price
     *
     * @param object $quote
     */
    public function collectTotals($quote)
    {
        foreach ($quote->getAllVisibleItems() as $item) {
            $itemId = $item->getId();
            $assignData = $this->getAssignDataByItemId($itemId);
            if ($assignData['assign_id'] > 0) {
                $assignId = $assignData['assign_id'];
                if ($assignData['child_assign_id'] > 0) {
                    $childAssignId = $assignData['child_assign_id'];
                    $price = $this->getAssocitePrice($assignId, $childAssignId);
                } else {
                    $price = $this->getAssignProductPrice($assignId);
                }
                $price = $this->getFinalPrice($price);
                $item->setCustomPrice($price);
                $item->setOriginalCustomPrice($price);
                $item->setRowTotal($item->getQty()*$price);
                $item->getProduct()->setIsSuperMode(true);
            }
        }
    }

    /**
     * Get Associated Assigned Product Price
     *
     * @param int $assignId
     * @param int $childAssignId
     * @return float
     */
    public function getAssocitePrice($assignId, $childAssignId)
    {
        $price = 0;
        $model = $this->_associates->create();
        $collection = $model->getCollection()
                            ->addFieldToFilter("parent_id", $assignId)
                            ->addFieldToFilter("id", $childAssignId);
        foreach ($collection as $item) {
            $price = $item->getPrice();
        }
        return $price;
    }

    /**
     * Get Associated Assigned Product Quantity
     *
     * @param int $assignId
     * @param int $childAssignId
     * @return int
     */
    public function getAssociteQty($assignId, $childAssignId)
    {
        $qty = 0;
        $model = $this->_associates->create();
        $collection = $model->getCollection()
                            ->addFieldToFilter("parent_id", $assignId)
                            ->addFieldToFilter("id", $childAssignId);
        foreach ($collection as $item) {
            $qty = $item->getQty();
        }
        return $qty;
    }

    /**
     * Validate Data
     *
     * @param array $data
     * @return array
     */
    public function validateData($data, $type)
    {
        if ($type == "configurable") {
            return $this->validateConfigData($data);
        }
        $result = ['error' => false, 'msg' => ''];
        $msg = "";
        try {
            if (trim($data['product_condition']) == "") {
                $msg .= "Product condition is required field.";
                $result['error'] = true;
            }
            if (trim($data['price']) == "") {
                $msg .= "Price is required field.";
                $result['error'] = true;
            } else {
                if (!is_numeric(trim($data['price']))) {
                    $msg .= "Price should be numeric.";
                    $result['error'] = true;
                }
            }
            if (trim($data['qty']) == "") {
                $msg .= "Quantity is required field.";
                $result['error'] = true;
            } else {
                if (!is_numeric(trim($data['qty']))) {
                    $msg .= "Quantity should be numeric.";
                    $result['error'] = true;
                }
            }
            if (trim($data['description']) == "") {
                $msg .= "Description is required field.";
                $result['error'] = true;
            }
            $result['msg'] = $msg;
        } catch (\Exception $e) {
            $result['error'] = true;
            $result['msg'] = "Something went wrong.";
        }
        return $result;
    }

    /**
     * Validate Price
     *
     * @param int $price
     * @return array
     */
    public function validatePrice($price)
    {
        $result = ["error" => false, "msg" => ""];
        $msg = "";
        if (trim($price) == "") {
            $msg = "Price is required field.";
            $result['error'] = true;
        } else {
            if (!is_numeric(trim($price))) {
                $msg = "Price should be numeric.";
                $result['error'] = true;
            }
        }
        $result['msg'] = $msg;
        return $result;
    }

    /**
     * Validate Quantity
     *
     * @param int $qty
     * @return array
     */
    public function validateQty($qty)
    {
        $result = ["error" => false, "msg" => ""];
        $msg = "";
        if (trim($qty) == "") {
            $msg = "Quantity is required field.";
            $result['error'] = true;
        } else {
            if (!is_numeric(trim($qty))) {
                $msg = "Quantity should be numeric.";
                $result['error'] = true;
            }
        }
        $result['msg'] = $msg;
        return $result;
    }

    /**
     * Validate Data
     *
     * @param array $data
     * @return array
     */
    public function validateConfigData($data)
    {
        $result = ['error' => false, 'msg' => ''];
        $msg = "";
        try {
            if (trim($data['product_condition']) == "") {
                $msg .= "Product condition is required field.";
                $result['error'] = true;
            }
            if (trim($data['description']) == "") {
                $msg .= "Description is required field.";
                $result['error'] = true;
            }
            $count = 0;
            foreach ($data['products'] as $productId => $info) {
                if (array_key_exists("id", $info)) {
                    $validateQty = $this->validateQty($info['qty']);
                    $validatePrice = $this->validatePrice($info['price']);
                    if ($validateQty['error']) {
                        continue;
                    }
                    if ($validatePrice['error']) {
                        continue;
                    }
                    $count++;
                }
            }
            if ($count == 0) {
                $msg .= "Please select associated products.";
                $result['error'] = true;
            }
            $result['msg'] = $msg;
        } catch (\Exception $e) {
            $result['error'] = true;
            $result['msg'] = "Something went wrong.";
        }
        return $result;
    }

    /**
     * Get Associated Product Collection
     *
     * @param int $productId
     * @return object
     */
    public function getAssignProductCollection($productId)
    {
        $itemsCollection = $this->_itemsCollection->create()
                            ->addFieldToFilter('assign_product_id', $productId)->getFirstItem();
        if ($itemsCollection->getProductId()) {
            $productId = $itemsCollection->getProductId();
        }
        $collection = $this->_itemsCollection->create();
        $joinTable = $this->_resource->getTableName('marketplace_datafeedback');
        $sql = 'mp.seller_id = main_table.seller_id';
        $sql .= ' and mp.status = 1';
        $fields = [];
        $fields[] = 'status';
        $fields[] = 'seller_id as mp_seller_id';
        $fields[] = "sum(mp.feed_price+mp.feed_value+mp.feed_quality) as total_rating";
        $fields[] = "count(mp.seller_id) as count";
        $collection->getSelect()->joinLeft($joinTable.' as mp', $sql, $fields);
        $field = 'sum(mp.feed_price+mp.feed_value+mp.feed_quality)/(count(mp.seller_id)*3)';
        $collection->getSelect()->columns(['rating' => new \Zend_Db_Expr($field)]);

        $joinTable = $this->_resource->getTableName('marketplace_userdata');
        $sql = 'mpud.seller_id = main_table.seller_id';
        $fields = [];
        $fields[] = 'shop_url';
        $fields[] = 'shop_title';
        $fields[] = 'logo_pic';
        $fields[] = 'is_seller';
        $collection->getSelect()->joinLeft($joinTable.' as mpud', $sql, $fields);
        $collection->getSelect()->group('main_table.seller_id')->where('mpud.is_seller = 1');
        $collection->addFieldToFilter(
            ['product_id','assign_product_id'],
            [
                                ['eq' => $productId],
                                ['eq' => $productId]
                            ]
        );
        return $collection;
    }

    /**
     * Get Allowed Product Types
     *
     * @return array
     */
    public function getAllowedProductTypes()
    {
        return $this->_allowedProductTypes;
    }

    /**
     * Get Associates Data
     *
     * @param int $assignId
     * @return array
     */
    public function getAssociatesData($assignId)
    {
        $result = [];
        $websiteId = $this->_storeManager->getWebsite()->getId();
        $model = $this->_associates->create();
        $collection = $model->getCollection()->addFieldToFilter("parent_id", $assignId);
        $proPriceAttrId = $this->eavAttribute->getIdByCode("catalog_product", "price");
        $catalogProductEntityDecimal = $model->getCollection()->getTable('catalog_product_entity_decimal');
        $catalogInventoryStockItem = $model->getCollection()->getTable('cataloginventory_stock_item');
        $collection->getSelect()->joinLeft(
            $catalogProductEntityDecimal.' as cped',
            'main_table.assign_product_id = cped.entity_id and cped.store_id = 0
            AND cped.attribute_id = '.$proPriceAttrId,
            ["product_price" => "value"]
        );
        $collection->getSelect()->join(
            $catalogInventoryStockItem.' as csi',
            'main_table.assign_product_id = csi.product_id',
            ["assign_qty" => "qty"]
        )->where("csi.website_id = 0 OR csi.website_id = ".$websiteId);
        foreach ($collection as $item) {
            $info = [
                    'id' => $item->getId(),
                    'qty' => $item->getAssignQty(),
                    'price' => number_format($item->getProductPrice(), 2),
                    'assign_product_id' => $item->getAssignProductId()
                ];
            $result[$item->getProductId()] = $info;
        }
        return $result;
    }

    /**
     * Get Associated Options of Assign Product
     *
     * @param int $productId
     * @return array
     */
    public function getAssociatedOptions($productId, $viewProductId = 0)
    {
        $result = [];
        $associateData = [];
        $parentId = $this->_items->create()->load($viewProductId, 'assign_product_id')->getId();
        $websiteId = $this->_storeManager->getWebsite()->getId();
        $model = $this->_associates->create();
        $associateCollection = $model->getCollection()->addFieldToFilter('parent_id', $parentId);
        foreach ($associateCollection as $associateProduct) {
            $associateData[$associateProduct->getProductId()] = $associateProduct->getAssignProductId();
        }
        $collection = $model->getCollection()
                    ->addFieldToFilter("parent_product_id", $productId);
        $proPriceAttrId = $this->eavAttribute->getIdByCode("catalog_product", "price");
        $catalogProductEntityDecimal = $model->getCollection()->getTable('catalog_product_entity_decimal');
        $catalogInventoryStockItem = $model->getCollection()->getTable('cataloginventory_stock_item');
        $collection->getSelect()->joinLeft(
            $catalogProductEntityDecimal.' as cped',
            'main_table.assign_product_id = cped.entity_id and cped.store_id = 0
            AND cped.attribute_id = '.$proPriceAttrId,
            ["product_price" => "value"]
        );
        $collection->getSelect()->join(
            $catalogInventoryStockItem.' as csi',
            'main_table.assign_product_id = csi.product_id',
            ["assign_qty" => "qty"]
        )->where("csi.website_id = 0 OR csi.website_id = ".$websiteId);
        $productInfo = $this->getAssociatedOptionsForOriginal($productId);
        foreach ($collection as $item) {
            if ($parentId != $item->getParentId()) {
                $info = [
                        'id' => $item->getId(),
                        'qty' => $item->getAssignQty(),
                        'price' => number_format($this->convertPriceFromBase($item->getProductPrice()), 2)
                    ];
                $productId = $item->getProductId();
                $itemProductId = $associateData[$productId] ?? $item->getProductId();
                $result[$itemProductId][$item->getParentId()] = $info;
            }
            $assignProductId = $item->getAssignProductId();
            if (isset($productInfo[$item->getProductId()])) {
                $result[$assignProductId][0] = $productInfo[$item->getProductId()];
            }
        }
        return $result;
    }

    public function getAssociatedOptionsForOriginal($productId)
    {
        $productInfo = [];
        $websiteId = $this->_storeManager->getWebsite()->getId();
        $model = $this->_associates->create();
        $collection = $model->getCollection()->addFieldToFilter("parent_product_id", $productId);
        $proPriceAttrId = $this->eavAttribute->getIdByCode("catalog_product", "price");
        $catalogProductEntityDecimal = $model->getCollection()->getTable('catalog_product_entity_decimal');
        $catalogInventoryStockItem = $model->getCollection()->getTable('cataloginventory_stock_item');
        $collection->getSelect()->joinLeft(
            $catalogProductEntityDecimal.' as cped',
            'main_table.product_id = cped.entity_id and cped.store_id = 0 AND cped.attribute_id = '.$proPriceAttrId,
            ["product_price" => "value"]
        );
        $collection->getSelect()->join(
            $catalogInventoryStockItem.' as csi',
            'main_table.product_id = csi.product_id',
            ["assign_qty" => "qty"]
        )->where("csi.website_id = 0 OR csi.website_id = ".$websiteId);
        foreach ($collection as $item) {
            if (!isset($productInfo[$item->getProductId()])) {
                $productInfo[$item->getProductId()] = [
                'id' => 0,
                'qty' => $item->getAssignQty(),
                'price' => number_format($this->convertPriceFromBase($item->getProductPrice()), 2)
                ];
            }
        }
        return $productInfo;
    }
    /**
     * Get Currenct Currency Symbol
     *
     * @return string
     */
    public function getCurrencySymbol()
    {
        return $this->_storeManager->getStore()->getCurrentCurrencyCode();
    }

    /**
     * Get Admin Email Id.
     *
     * @return string
     */
    public function getAdminEmail()
    {
        $config = 'mpassignproduct/settings/admin_email';
        return $this->_scopeConfig->getValue($config);
    }

    /**
     * Get Admin Email Name.
     *
     * @return string
     */
    public function getAdminName()
    {
        $config = 'mpassignproduct/settings/admin_name';
        return $this->_scopeConfig->getValue($config);
    }

    /**
     * Get Product Edit Message.
     *
     * @return string
     */
    public function getEditProductMessage()
    {
        $config = 'mpassignproduct/settings/edit_msg';
        return $this->_scopeConfig->getValue($config);
    }

    /**
     * Get Product Add Message.
     *
     * @return string
     */
    public function getAddProductMessage()
    {
        $config = 'mpassignproduct/settings/add_msg';
        return $this->_scopeConfig->getValue($config);
    }

    /**
     * Send Product Email
     *
     * @param array $data
     * @param booln $isEdit
     */
    public function sendProductMail($data, $isEdit = false)
    {
        try {
            $customer = $this->_customerSession->getCustomer();
            $adminEmail = $this->getAdminEmail();
            $adminName = $this->getAdminName();
            if ($adminEmail != '') {
                if (!($seller = $this->getSellerDetails($this->getCustomerId()))) {
                    return;
                }
                $shopTitle = $seller->getShopTitle();
                if (!$shopTitle) {
                    $shopTitle = $seller->getShopUrl();
                }
                $store = $this->_storeManager->getStore()->getId();
                $product = $this->getProduct($data['product_id']);
                $productName = $product->getName();
                $this->_template = "product_template";
                if ($isEdit) {
                    $msg = $this->getEditProductMessage();
                    $subject = __("Update Product");
                } else {
                    $msg = $this->getAddProductMessage();
                    $subject = __("Assigned Product");
                }
                $condition = $data['condition'];
                if ($condition == 1) {
                    $condition = __("New");
                } else {
                    $condition = __("Used");
                }
                $templateVars = [
                                    'store' => $this->_storeManager->getStore(),
                                    'subject' => $subject,
                                    'message' => $msg,
                                    'admin_name' => $adminName,
                                    'seller_name' => $shopTitle,
                                    'product_name' => $productName,
                                    'product_condition' => $condition,
                                    'msg' => $msg,
                                ];
                $senderInfo = ['email' => $customer->getEmail(), 'name' => $shopTitle];
                $receiverInfo = [
                    'name' => $adminName,
                    'email' => $adminEmail,
                ];
                $this->_inlineTranslation->suspend();
                $this->generateTemplate($templateVars, $senderInfo, $receiverInfo, $store);
                try {
                    $transport = $this->_transportBuilder->getTransport();
                    $transport->sendMessage();
                } catch (\Exception $e) {
                    $this->_messageManager->addError($e->getMessage());
                }
                 $this->_inlineTranslation->resume();
            }
        } catch (\Excecption $e) {
            $error = $e->getMessage();
        }
    }

    protected function generateTemplate($emailTemplateVariables, $senderInfo, $receiverInfo, $storeId = 0)
    {
        if (!$storeId) {
            $storeId = $this->_storeManager->getStore()->getId();
        }
        $template =  $this->_transportBuilder->setTemplateIdentifier($this->_template)
            ->setTemplateOptions(
                [
                    'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                    'store' => $storeId
                ]
            )
            ->setTemplateVars($emailTemplateVariables)
            ->setFrom($senderInfo)
            ->addTo($receiverInfo['email'], $receiverInfo['name']);
        return $this;
    }

    /**
     * Send Status Mail To Seller
     *
     * @param object $assignProduct
     * @param integer $flag
     */
    public function sendStatusMail($assignProduct, $flag = 0)
    {
        try {
            $adminEmail = $this->getAdminEmail();
            $adminName = $this->getAdminName();
            if ($adminEmail != '') {
                if ($assignProduct->getId() <= 0) {
                    return;
                }
                $sellerId = $assignProduct->getSellerId();
                $product = $this->getProduct($assignProduct->getProductId());
                if (!($seller = $this->getSellerDetails($sellerId))) {
                    return;
                }
                $shopTitle = $seller->getShopTitle();
                if (!$shopTitle) {
                    $shopTitle = $seller->getShopUrl();
                }
                $customer = $this->_customer->create()->load($sellerId);
                $sellerName = $customer->getFirstname();
                $store = $this->_storeManager->getStore()->getId();
                $productName = $product->getName();
                if ($flag == 0) {
                    $this->_template = "product_approve";
                    $msg = __("Your assigned product for '%1' is approved.", $productName);
                } else {
                    $this->_template = "product_disapprove";
                    $msg = __("Your assigned product for '%1' is disapproved.", $productName);
                }
                $templateVars = [
                                    'store' => $this->_storeManager->getStore(),
                                    'seller_name' => $sellerName,
                                    'msg' => $msg,
                                ];
                $senderInfo = ['email' => $adminEmail, 'name' => $adminName];
                $receiverInfo = [
                    'name' => $sellerName,
                    'email' => $customer->getEmail(),
                ];
                $this->_inlineTranslation->suspend();
                $this->generateTemplate($templateVars, $senderInfo, $receiverInfo, $store);
                try {
                    $transport = $this->_transportBuilder->getTransport();
                    $transport->sendMessage();
                } catch (\Exception $e) {
                    $this->_messageManager->addError($e->getMessage());
                }
                 $this->_inlineTranslation->resume();
            }
        } catch (\Excecption $e) {
            $error = $e->getMessage();
        }
    }

    /**
     * Get Product Id By Quote Item Id
     *
     * @param int $quoteItemId
     * @return int $productId
     */
    public function getProductFromItemId($quoteItemId)
    {
        $productId = 0;
        $item = $this->_quoteOption
                    ->create()
                    ->getCollection()
                    ->addFieldToFilter("item_id", $quoteItemId)
                    ->addFieldToFilter("code", "simple_product")
                    ->getFirstItem();
        if ($item) {
            $productId = $item->getProductId();
        }
        return $productId;
    }

    /**
     * Get Image Collection
     *
     * @param int $assignId
     * @return collection object
     */
    public function getImagesCollection($assignId)
    {
        $collection = $this->_dataCollection->create();
        $collection->addFieldToFilter('type', 1);
        $collection->addFieldToFilter("assign_id", $assignId);
        return $collection;
    }

    /**
     * Manage Image
     *
     * @param array|object $data
     * @param array $result
     * @return void
     */
    public function manageImages($data, $result)
    {
        $data = $data;
        $totalImages = (int) $this->_request->getParam('total');
        $baseImage = (int) $this->_request->getParam('base_image');
        $deletedImages = trim($this->_request->getParam('delete_ids'));
        $assignId = $result['assign_id'];
        $this->uploadImages($totalImages, $assignId);
        $this->deleteImages($deletedImages, $assignId);
        $this->setBaseImage($baseImage, $assignId);
    }

    /**
     * Set Base Image for Assigned Product
     *
     * @param int $baseImage
     * @param int $assignId
     * @return void
     */
    public function setBaseImage($baseImage, $assignId)
    {
        $store_id = $this->_storeManager->getStore()->getStoreId();
        $update = true;
        if ($baseImage == 0) {
            $old_base = $this->_dataCollection->create()
                        ->addFieldToFilter("type", 1)
                        ->addFieldToFilter("is_default", 1)
                        ->addFieldToFilter("assign_id", $assignId)
                        ->addFieldToFilter("store_view", $store_id);
            if ($old_base->getSize()) {
                foreach ($old_base as $key) {
                    $key->setIsDefault(0)->save();
                }
            }
            return;
        }
        try {
            $collection = $this->_dataCollection->create();
            $collection->addFieldToFilter("type", 1);
            $collection->addFieldToFilter("assign_id", $assignId);
            $collection->setPageSize($baseImage)->setCurPage(1);
            $item = $collection->getLastItem();
            $baseImageId = $item->getId();

            $old_base = $this->_dataCollection->create()
                        ->addFieldToFilter("type", 1)
                        ->addFieldToFilter("is_default", 1)
                        ->addFieldToFilter("assign_id", $assignId)
                        ->addFieldToFilter("store_view", ['eq' => $store_id]);
            if ($old_base->getSize()) {
                foreach ($old_base as $key) {
                    if ($baseImageId != $key->getId()) {
                        $key->setIsDefault(0)->save();
                    } else {
                        $update = false;
                    }
                }
            }
            if ($update) {
                $data = [];
                $data['is_default'] = 1;
                $data['store_view'] = $store_id;
                $collection = $this->_data->create()->load($baseImageId);
                $collection->addData($data)->setId($baseImageId)->save();
            }
        } catch (\Exception $e) {
            $e->getMessage();
        }
    }

    /**
     * Delete Assigned Product Images
     *
     * @param array $deletedImages
     * @param int $assignId
     * @return void
     */
    public function deleteImages($deletedImages, $assignId)
    {
        if ($deletedImages == "") {
            return;
        }
        if (strpos($deletedImages, ",") !== false) {
            $deletedImages = explode(",", $deletedImages);
        } else {
            $deletedImages = [$deletedImages];
        }
        $collection = $this->_dataCollection->create();
        $collection->addFieldToFilter("type", 1);
        $collection->addFieldToFilter("assign_id", $assignId);
        $collection->addFieldToFilter("id", ["in" => $deletedImages]);
        if ($collection->getSize()) {
            $path = $this->_filesystem
                        ->getDirectoryRead(DirectoryList::MEDIA)
                        ->getAbsolutePath('marketplace/assignproduct/product/');
            $path .= $assignId;
            foreach ($collection as $item) {
                $imagePath = $path."/".$item->getValue();
                $this->_fileDriver->deleteFile($imagePath);
                $this->deleteItem($item);
            }
        }
    }

    /**
     * Upload All Images of Rma
     *
     * @param int $numberOfImages
     * @param int $id
     */
    public function uploadImages($numberOfImages, $assignId)
    {
        if ($numberOfImages > 0) {
            $uploadPath = $this->_filesystem
                                ->getDirectoryRead(DirectoryList::MEDIA)
                                ->getAbsolutePath('marketplace/assignproduct/product/');
            $uploadPath .= $assignId;
            $count = 0;
            for ($i = 0; $i < $numberOfImages; $i++) {
                $count++;
                $fileId = "showcase[$i]";
                $this->uploadImage($fileId, $uploadPath, $assignId, $count);
            }
        }
    }

    /**
     * Upload Image of Rma
     *
     * @param string $fileId
     * @param string $uploadPath
     * @param int $count
     */
    public function uploadImage($fileId, $path, $id, $count)
    {
        $allowedExtensions = ['png', 'jpg', 'jpeg', 'gif'];
        try {
            $uploader = $this->_fileUploader->create(['fileId' => $fileId]);
            $uploader->setAllowedExtensions($allowedExtensions);
            $imageData = $uploader->validateFile();
            $name = $imageData['name'];
            $ext = explode('.', $name);
            $ext = strtolower(end($ext));
            $time = time() + $count;
            $imageName = 'image-'.$time.'.'.$ext;
            $uploader->setAllowRenameFiles(true);
            $uploader->setFilesDispersion(false);
            $uploader->save($path, $imageName);
            $data = [];
            $data['type'] = 1;
            $data['assign_id'] = $id;
            $data['value'] = $imageName;
            $data['is_default'] = 0;
            $data['status'] = 1;
            $this->_data->create()->setData($data)->save();
        } catch (\Exception $e) {
            $error =  true;
        }
    }

    /**
     * Get Image Url for Assign Product
     *
     * @param string $image
     *
     * @return string
     */
    public function getBaseImageUrl($assignId)
    {
        $currentStore = $this->_storeManager->getStore();
        $mediaUrl = $currentStore->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
        $imageUrl = $mediaUrl.'marketplace/assignproduct/product/'.$assignId."/";
        return $imageUrl;
    }

    public function showProfile()
    {
        $config = 'marketplace/profile_settings/seller_profile_display';
        $showProfile = $this->_scopeConfig->getValue($config);
        if ($showProfile == '') {
            return false;
        }
        return $showProfile;
    }

    /**
     * Get Curreny Symbol
     *
     * @return $this
     */
    public function getCurrencyCode()
    {
        return $this->_storeManager->getStore()->getCurrentCurrency()->getCode();
    }

    /**
     * Convert Amount into Store Currency
     *
     * @param int|float $amount
     * @return $this
     */
    public function convertPriceFromBase($amount)
    {
        $currency = null;
        $store = $this->_storeManager->getStore()->getStoreId();
        $rate = $this->_priceCurrency->convert($amount, $store, $currency);
        return $this->_priceCurrency->round($rate);
    }

    /**
     * Convert Price To Base
     *
     * @param int|float $amount
     * @return $this
     */
    public function convertPriceToBase($amount)
    {
        $currency = null;
        $store = $this->_storeManager->getStore()->getStoreId();
        $rate = $this->_priceCurrency->convert($amount, $store, $currency);
        $amount = $amount / $rate;
        return $this->_priceCurrency->round($amount);
    }

    /**
     * Get Final Price
     *
     * @param int|float $price
     * @return $price
     */
    public function getFinalPrice($price)
    {
        $baseCurrencyCode = $this->getBaseCurrencyCode();
        $currentCurrencyCode = $this->getCurrentCurrencyCode();
        if ($baseCurrencyCode !== $currentCurrencyCode) {
            return $this->convertPriceFromBase($price);
        }
        return $price;
    }

    /**
     * Get store base currency code
     *
     * @return string
     */
    public function getBaseCurrencyCode()
    {
        return $this->_storeManager->getStore()->getBaseCurrencyCode();
    }

    /**
     * Get current store currency code
     *
     * @return string
     */
    public function getCurrentCurrencyCode()
    {
        return $this->_storeManager->getStore()->getCurrentCurrencyCode();
    }

    /**
     * Get default store currency code
     *
     * @return string
     */
    public function getDefaultCurrencyCode()
    {
        return $this->_storeManager->getStore()->getDefaultCurrencyCode();
    }

    /**
     * Get allowed store currency codes
     *
     * If base currency is not allowed in current website config scope,
     * then it can be disabled with $skipBaseNotAllowed
     *
     * @param bool $skipBaseNotAllowed
     * @return array
     */
    public function getAvailableCurrencyCodes($skipBaseNotAllowed = false)
    {
        return $this->_storeManager->getStore()->getAvailableCurrencyCodes($skipBaseNotAllowed);
    }

    /**
     * Get array of installed currencies for the scope
     *
     * @return array
     */
    public function getAllowedCurrencies()
    {
        return $this->_storeManager->getStore()->getAllowedCurrencies();
    }

    /**
     * Get current currency rate
     *
     * @return float
     */
    public function getCurrentCurrencyRate()
    {
        return $this->_storeManager->getStore()->getCurrentCurrencyRate();
    }

    /**
     * Get currency symbol for current locale and currency code
     *
     * @return string
     */
    public function getCurrentCurrencySymbol()
    {
        return $this->_priceCurrency->getCurrencySymbol();
    }

    /**
     * Get Media Url
     *
     * @return string
     */
    public function getBaseMediaUrl()
    {
        $currentStore = $this->_storeManager->getStore();
        return $currentStore->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
    }

    /**
     * Get Assigned Associated Products
     *
     * @param int $assignId
     * @return collection object
     */
    public function getAssignedAssociatedProducts($assignId)
    {
        $collection = $this->_productCollection->create()->addAttributeToSelect("name");

        $joinTable = $this->_resource
                        ->getTableName('marketplace_assignproduct_associated_products');
        $sql = 'maas.product_id = e.entity_id';
        $sql .= ' and maas.parent_id = '.$assignId;
        $fields = ['qty', 'price', 'product_id', 'assign_product_id'];
        $collection->getSelect()->join($joinTable.' as maas', $sql, $fields);
        return $collection;
    }

    /**
     * Get Assigned Associated Products
     *
     * @param int $productId
     * @param int $id
     * @return collection object
     */
    public function getAssignedAssociatedProduct($productId, $id)
    {
        $collection = $this->_associates->create()->getCollection()
                      ->addFieldToFilter("parent_product_id", $productId)
                      ->addFieldToFilter("parent_id", $id);
        return $collection;
    }

    /**
     * Get Current Product
     *
     * @return $this
     */
    public function getCurrentProduct()
    {
        return $this->_coreRegistry->registry("current_product");
    }

    /**
     * Get Assigned Product Status
     *
     * @param int $assignId
     * @return boolean
     */
    public function isEnabled($assignId)
    {
        $model = $this->_items->create()->load($assignId);
        return $model->getStatus();
    }

    /**
     * Load Customer
     *
     * @param int $customerId
     * @return object
     */
    public function getCustomer($customerId)
    {
        return $this->_customer->create()->load($customerId);
    }

    /**
     * Get Seller's Details
     *
     * @param int $customerId
     * @return collection
     */
    public function getSellerInfo($customerId)
    {
        $result = [];
        $collection = $this->_customer->create()->getCollection();
        $joinTable = $this->_resource->getTableName('marketplace_userdata');
        $sql = 'mpud.seller_id = e.entity_id';
        $fields = [];
        $fields[] = 'shop_url';
        $fields[] = 'shop_title';
        $fields[] = 'is_seller';
        $collection->getSelect()->joinLeft($joinTable.' as mpud', $sql, $fields);
        $collection->addFieldToFilter("entity_id", $customerId);
        $collection->getSelect()->where("mpud.store_id = 0");
        return $collection->getFirstItem();
    }

    /**
     * getAssocatedProductQty function
     *
     * @param [int] $productId
     * @return qty
     */
    public function getAssocatedProductQty($productId)
    {
        $qty = 0;
        $collection = $this->getCollection();
        $joinTable = $this->_resource
                        ->getTableName('marketplace_assignproduct_associated_products');
        $sql = 'maas.parent_id = main_table.id';
        $sql .= ' and main_table.status = 1';
        $fields = ['qty as assocqty'];
        $collection->getSelect()->join($joinTable.' as maas', $sql, $fields)
            ->where('maas.product_id= '.$productId);
        $collection->addFilterToMap('status', 'main_table.status');

        foreach ($collection as $key) {
            $qty += $key->getAssocqty();
        }
        return $qty;
    }

    /**
     * function to get Base Image store view
     *
     * @param [int] $id //assign_id
     * @return string
     */
    public function getBaseImage($id)
    {
        $store_id = $this->_storeManager->getStore()->getId();
        $collection = $this->_data->create()->getCollection()
                ->addFieldToFilter('assign_id', $id)
                ->addFieldToFilter('is_default', 1)
                ->addFieldToFilter('type', 1)
                ->addFieldToFilter('store_view', $store_id);
        if ($collection->getSize()) {
            $item = $collection->getLastItem();
            return $item->getValue();
        } else {
            return '';
        }
    }

    /**
     * get current store
     *
     * @return void
     */
    public function getStore()
    {
        return $this->_storeManager->getStore();
    }

    /**
     * manage description according to store view
     *
     * @param [array] $data
     * @param [int] $assignId
     * @return void
     */
    public function manageDescription($data, $assignId)
    {
        $store_id = $this->getStore()->getId();
        $collection = $this->_data->create()->getCollection()
                    ->addFieldToFilter('assign_id', $assignId)
                    ->addFieldToFilter('is_default', 1)
                    ->addFieldToFilter('type', 2)
                    ->addFieldToFilter('store_view', $store_id);
        if ($collection->getSize()) {
            foreach ($collection as $key) {
                $key->setValue($data['description'])->setId($key->getId())->save();
            }
        } else {
            $descData = [
                'type' => 2,
                'assign_id' => $assignId,
                'value' => $data['description'],
                'is_default' => 1,
                'store_view' => $store_id,
                'status' => 1
            ];
            $model = $this->_data->create();
            $model->setData($descData)->save();
        }
    }

    /**
     * get Description according to store view
     *
     * @param [int] $assignId
     * @return string
     */
    public function getDescription($assignId)
    {
        $store_id = $this->getStore()->getId();
        $desc = '';
        $collection = $this->_data->create()->getCollection()
                    ->addFieldToFilter('assign_id', $assignId)
                    ->addFieldToFilter('is_default', 1)
                    ->addFieldToFilter('type', 2)
                    ->addFieldToFilter('store_view', $store_id);
        if ($collection->getSize()) {
            foreach ($collection as $key) {
                $desc = $key->getValue();
            }
        } else {
            $collection = $this->_data->create()->getCollection()
                    ->addFieldToFilter('assign_id', $assignId)
                    ->addFieldToFilter('is_default', 1)
                    ->addFieldToFilter('type', 2);
            foreach ($collection as $key) {
                $desc = $key->getValue();
                break;
            }
        }
        return $desc;
    }

    public function getMinimumPriceProducts($productId)
    {
        if ($this->showMinimumPrice()) {
            $assignProductIds = $this->getCollection()->addFieldToFilter('product_id', $productId)->getAllIds();
            if (!count($assignProductIds)) {
                return false;
            }
            $assignProductIds[] = $productId;
            $productCollection = $this->getProductCollection()
                          ->addAttributeToSelect('price')
                          ->addFieldToFilter('entity_id', ['in' => $assignProductIds]);
            $productCollection->addAttributeToSort('price', 'ASC');
            return $productCollection->getFirstItem()->getId();
        } else {
            return false;
        }
        return false;
    }

    /**
     * get minimum price product details
     *
     * @param [int] $productId
     * @param string $type
     * @return object|bool
     */
    public function getMinimumPriceProductDetails($productId, $type = '-')
    {
        if ($this->showMinimumPrice()) {
            $products = [];
            $tempProducts = [];
            if ($type == "configurable") {
                $model = $this->_associates->create();
                $collection = $model->getCollection()
                            ->addFieldToFilter("parent_product_id", $productId)
                            ->addFieldToFilter('price', ['neq' => 0]);
                foreach ($collection as $key => $item) {
                    $products[$key] = [
                        'price' => $item->getPrice(),
                        'id' => $item->getId(),
                        'condition' => $this->getAssignProduct($item->getParentId())->getCondition(),
                        'parentId' => $item->getParentId()
                    ];
                }

                $configProductChildrens = $this->getConfigProductChildrens($productId);
                if (is_array($configProductChildrens) || is_object($configProductChildrens)) {
                    foreach ($configProductChildrens as $child) {
                        $products[] = [
                            'price' => $child['price'],
                            'id' => 0,
                            'condition' => 1,
                            'parentId' => 0,
                        ];
                    }
                }
            } else {
                $totalProducts = $this->getTotalProducts($productId);
                foreach ($totalProducts as $key => $product) {
                    $products[$key] = [
                        'price' => $product['price'],
                        'id' => $product['assign_id'],
                        'condition' => (isset($product['condition'])) ? $product['condition'] : '',
                    ];
                }
            }

            if (count($products) == 0) {
                return false;
            }
            $tempProducts = $products;

            usort($products, function ($a, $b) {
                return $a['price'] <=> $b['price'];
            });
            usort($tempProducts, function ($a, $b) {
                return $a['id'] <=> $b['id'];
            });

            if ($products[0]['price'] == $tempProducts[0]['price']) {
                return $tempProducts;
            }
            if ($products[0]['condition'] == 2) {
                if ($type == "configurable") {
                    $minPriceProduct = $this->getMinimumPriceConfigProductDetails('', $products);
                    array_splice($products, 0, count($minPriceProduct));
                    array_splice($products, count($minPriceProduct), 0, $minPriceProduct);
                } else {
                    $this->moveElement($products, 0, 1);
                }
            }
            return $products;
        } else {
            return false;
        }
    }

    /**
     * function to Move array element
     *
     * @param array $array
     * @param int $from
     * @param int $to
     */
    public function moveElement(&$array, $from, $to)
    {
        $out = array_splice($array, $from, 1);
        array_splice($array, $to, 0, $out);
    }

    /**
     * Get Config Product Childrens
     *
     * @param [int] $productId
     * @return array
     */
    public function getConfigProductChildrens($productId)
    {
        $mainProduct = $this->getProduct($productId);
        $stockState = $this->stockStateInterface;
        $productTypeInstance = $mainProduct->getTypeInstance();
        $associateProducts = $productTypeInstance->getUsedProducts($mainProduct);
        $products = [];
        foreach ($associateProducts as $child) {
            $stock = $stockState->getStockQty($child->getId(), $child->getStore()->getWebsiteId());
            $products[] = [
                'price' => money_format("%.2n", $child->getPrice()),
                'stock' => $stock,
                'id' => $child->getId(),
            ];
        }
        return $products;
    }

    /**
     * Get Minimum Price Config Product Details
     *
     * @param [int] $productId
     * @param array $products
     * @return array||bool
     */
    public function getMinimumPriceConfigProductDetails($productId, $products = [])
    {
        $tempProduct = [];
        if ($productId) {
            $products = [];
            $products = $this->getMinimumPriceProductDetails($productId, 'configurable');
        }
        if ($products && !empty($products)) {
            $id = $products[0]['parentId'];
            foreach ($products as $item) {
                if ($item['parentId'] == $id) {
                    $tempProduct[] = $item;
                }
            }
            return $tempProduct;
        } else {
            return false;
        }
    }

    /**
     * Get Custom Product Options
     *
     * @param [object] $product
     * @return $this
     */
    public function getCustomOptions($product)
    {
        return $this->_customOptions->getProductOptionCollection($product);
    }

    /**
     * function to get StockStatus
     *
     * @param int $productId
     * @return string
     */
    public function checkInStock($productId)
    {
        $stockItem = $this->_stockRegistry->getStockItem($productId);
        return $stockItem->getIsInStock();
    }

    /**
     * function to check module is enabled or not
     *
     * @param string $module
     * @return boolean
     */
    public function isModuleEnabled($module)
    {
        return $this->_moduleManager->isOutputEnabled($module);
    }

    /**
     * function to get Configurable child products price
     *
     * @param [int] $productId
     * @return price
     */
    public function getConfigProducts($productId)
    {
        $configProduct = $this->_product->create()->load($productId);
        $price = [];
        $_children = $configProduct->getTypeInstance()->getUsedProducts($configProduct);
        foreach ($_children as $child) {
            $price[] = $child->getPrice();
        }
        return $price;
    }

    /**
     * @param [string] $string
     * @return string
     */
    public function getEscaper($string)
    {
        return $this->escaper->escapeHtml($string);
    }

    /**
     * Get Assigned Product Count
     *
     * @param $productId
     * @return collection size
     */
    public function assignProductsCount($productId)
    {
        $collection = $this->_itemsCollection->create();
        $collection->addFieldToFilter(
            ['product_id','assign_product_id'],
            [
                                ['eq'=> $productId],
                                ['eq'=> $productId],
                            ]
        );
        $collection->addFieldToFilter("status", 1);
        return $collection->getSize();
    }

    /**
     * Get Assigned Product Count
     *
     * @param $productId
     * @return collection size
     */
    public function getAssignProductsCount($productId)
    {
        $collection = $this->_itemsCollection->create()
                        ->addFieldToFilter("product_id", $productId)
                        ->addFieldToFilter("status", 1);
        return $collection->getSize();
    }
    /**
     * Get store identifier
     *
     * @return  int
     */
    public function getStoreId()
    {
        return $this->_storeManager->getStore()->getId();
    }
    /**
     * getStatusValueForProduct get product status value based on the configuration.
     * @param  integer $flag   [0 for new product and 1 for existing product]
     * @param  bool  $status   [Current Status of the product]
     * @return bool
     */
    public function getStatusValueForProduct($flag, $status)
    {
        if (!$flag) {
            if ($this->isAddApprovalRequired()) {
                $status = \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED;
            } else {
                $status = \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED;
            }
        } elseif ($this->isEditApprovalRequired()) {
            $status = \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED;
        }
        return $status;
    }
    /**
     * updateProductData is used to update the product attributes
     * @param  mixed  $productIds    [Product Ids]
     * @param  mixed  $attributData  [attribute's Data]
     * @param  integer $storeId      [store Id]
     * @return void
     */
    public function updateProductData($productIds, $attributData, $storeId = 0)
    {
        $this->productAction->updateAttributes($productIds, $attributData, (int) $storeId);
    }
    /**
     * deleteAssociatedProducts is used to delete the associated products
     * @param  int  $parentItemId    [Parent Product Id]
     * @return void
     */
    public function deleteAssociatedProducts($parentItemId = 0)
    {
        $associateProducts = $this->_associates->create()
                                ->getCollection()
                                ->addFieldToFilter('parent_id', $parentItemId);
        $productIds = $associateProducts->getAllIds();
        if (!empty($productIds)) {
            $productCollection = $this->_productCollection->create()
                                  ->addFieldToFilter('entity_id', ['in' => $productIds]);
            $productCollection->walk('delete');
            $associateProducts->walk('delete');
        }
    }
}
