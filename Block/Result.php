<?php

namespace Feecompass\Rankings\Block;

use \Magento\Framework\View\Element\Template;
use \Magento\Framework\View\Element\Template\Context;
use \Feecompass\Rankings\Helper\TokenService;

class Result extends Template
{

    public function __construct(Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    )
    {        
        $this->_storeManager = $storeManager;
        parent::__construct($context);
    }

    /* Set in Controller/Result/Result.php */
    public function getIdsData()
    {
        return $this->getIds();
    }

    /* Set in Controller/Result/Result.php */
    public function getLimitData()
    {
        return $this->getLimit();
    }


    public function getProductCollection(){
        $limitParam = $this->getLimitData();
        $idsParam = $this->getIdsData();
        $productIds = explode(",", $idsParam);
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $model = $objectManager->create('\Magento\Catalog\Model\Product');
        $collection = $model->getCollection()
            ->addAttributeToFilter('entity_id', array('in' => $productIds))
            ->addAttributeToSelect('*');
        $collection->getSelect()->limit($limitParam);
        $collection->getSelect()->order(new \Zend_Db_Expr('FIELD(entity_id,' . implode(',', $productIds).')'));
        $collection->load();

        return $collection;
    }
}