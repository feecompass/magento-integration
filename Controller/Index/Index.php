<?php

namespace Feecompass\Rankings\Controller\Index;

use \Magento\Framework\App\Action\Action;
use \Magento\Framework\App\Action\Context;
use \Magento\Framework\View\Result\PageFactory;


class Index extends Action implements \Magento\Csp\Api\CspAwareActionInterface
{

    /**
    * @var PageFactory
    */
    protected $resultPageFactory;


    /**
    * Result constructor.
    * @param Context $context
    * @param PageFactory $pageFactory
    */
    public function __construct(Context $context, PageFactory $pageFactory)
    {
        $this->resultPageFactory = $pageFactory;
        parent::__construct($context);
    }


    /**
    * The controller action
    *
    * @return \Magento\Framework\View\Result\Page
    */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        return $resultPage;
    }

    /*
    FetchPolicy arguments
        string $id,
        bool $noneAllowed = true,
        array $hostSources = [],
        array $schemeSources = [],
        bool $selfAllowed = false,
        bool $inlineAllowed = false,
        bool $evalAllowed = false,
        array $nonceValues = [],
        array $hashValues = [],
        bool $dynamicAllowed = false,
        bool $eventHandlersAllowed = false
    */
    public function modifyCsp(array $appliedPolicies): array
    {
/*        $appliedPolicies[] = new \Magento\Csp\Model\Policy\FetchPolicy(
            'font-src',
            false,
            [],
            [],
            false,
            false,
            false,
            [],
        );
*/        
        return $appliedPolicies;
    }
}