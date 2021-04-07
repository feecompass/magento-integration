<?php

namespace Feecompass\Rankings\Controller\Result;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\Result\JsonFactory;

use GuzzleHttp\Client;
use GuzzleHttp\ClientFactory;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ResponseFactory;
use Magento\Framework\Webapi\Rest\Request;


class Result extends \Magento\Framework\App\Action\Action
{

     /**
     * @var Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    protected $resultJsonFactory; 

    /**
     * @param Context     $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        JsonFactory $resultJsonFactory
        )
    {

        $this->resultPageFactory = $resultPageFactory;
        $this->resultJsonFactory = $resultJsonFactory; 
        return parent::__construct($context);
    }


    public function execute()
    {
				if (!$this->getRequest()->isXmlHttpRequest()) {
				    $this->getResponse()->setRedirect($this->getRequest()->getParam('lasturl'));
				}
        $ids = $this->getRequest()->getParam('ids');
        $limit = $this->getRequest()->getParam('limit');
        //$params = $this->getRequest()->getParams();
        $result = $this->resultJsonFactory->create();
        $resultPage = $this->resultPageFactory->create();

        $listBlock = $resultPage->getLayout()->getBlock("feecompass_result_result");
        $listBlock->setData('ids', $ids);
        $listBlock->setData('limit', $limit);
        $result->setData(["productsHtml" => $listBlock->toHtml()]);
        return $result;
    }
}
