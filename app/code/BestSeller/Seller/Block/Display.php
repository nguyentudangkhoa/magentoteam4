<?php

namespace BestSeller\Seller\Block;

class Display extends \Magento\Framework\View\Element\Template{
    /**
     * @var \Magento\Framework\App\ObjectManager
     */
    protected $objectManager;
    /**
     * @var string
     */
    protected $_template = 'widget/bestsellerdwidget.phtml';
    /**
     * @var \Magento\Reports\Model\ResourceModel\Report\Collection\Factory
     */
    protected $_resourceFactory;
    /**
     * @var \Magento\Catalog\Helper\Image
     */
    protected $_imageHelper;
    /**
     * @var \Magento\Checkout\Helper\Cart
     */
    protected $_cartHelper;
    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $product;
    /**
     * @var \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable
     */
    protected $_catalogProductTypeConfigurable;

    /**
     * Display constructor.
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Reports\Model\ResourceModel\Report\Collection\Factory $resourceFactory
     * @param \Magento\Catalog\Model\ProductFactory $product
     * @param \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable $catalogProductTypeConfigurable
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Reports\Model\ResourceModel\Report\Collection\Factory $resourceFactory,
        \Magento\Catalog\Model\ProductFactory $product,
        \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable $catalogProductTypeConfigurable,
        array $data = []
    ) {
        $this->_resourceFactory = $resourceFactory;
        $this->product = $product;
        $this->_imageHelper = $context->getImageHelper();
        $this->_cartHelper = $context->getCartHelper();
        $this->objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->_catalogProductTypeConfigurable = $catalogProductTypeConfigurable;
        parent::__construct($context, $data);
    }

    /**
     * @return \Magento\Catalog\Helper\Image
     */
    public function imageHelperObj(){
        return $this->_imageHelper;
    }

    /**
     * @param $id
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct($id){

        return $this->product->create()->load($id);
    }
    /**
    To get featured product collection
     */
    public function getBestsellerProduct(){
        $resourceCollection = $this->_resourceFactory->create('Magento\Sales\Model\ResourceModel\Report\Bestsellers\Collection');
        $resourceCollection->setPageSize(10);
        return $resourceCollection;
    }

    /**
     * @param $product
     * @param array $additional
     * @return string
     */
    public function getAddToCartUrl($product, $additional = [])
    {
        return $this->_cartHelper->getAddUrl($product, $additional);
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @param null $priceType
     * @param string $renderZone
     * @param array $arguments
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getProductPriceHtml(
        \Magento\Catalog\Model\Product $product,
        $priceType = null,
        $renderZone = \Magento\Framework\Pricing\Render::ZONE_ITEM_LIST,
        array $arguments = []
    ) {
        if (!isset($arguments['zone'])) {
            $arguments['zone'] = $renderZone;
        }
        $arguments['zone'] = isset($arguments['zone'])
            ? $arguments['zone']
            : $renderZone;
        $arguments['price_id'] = isset($arguments['price_id'])
            ? $arguments['price_id']
            : 'old-price-' . $product->getId() . '-' . $priceType;
        $arguments['include_container'] = isset($arguments['include_container'])
            ? $arguments['include_container']
            : true;
        $arguments['display_minimal_price'] = isset($arguments['display_minimal_price'])
            ? $arguments['display_minimal_price']
            : true;

        $priceRender = $this->getLayout()->getBlock('product.price.render.default');

        $price = '';
        if ($priceRender) {
            $price = $priceRender->render(
                \Magento\Catalog\Pricing\Price\FinalPrice::PRICE_CODE,
                $product,
                $arguments
            );
        }
        return $price;
    }
}
