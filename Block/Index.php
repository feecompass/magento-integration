<?php

namespace Feecompass\Rankings\Block;

use \Magento\Framework\View\Element\Template;
use \Magento\Framework\View\Element\Template\Context;
use \Feecompass\Rankings\Helper\TokenService;
use \Feecompass\Rankings\Model\Config as FeecompassConfig;

class Index extends Template
{

    const FEECOMPASS_PROFILE_PATH = '/feecompass-profile.js';
    const FEECOMPASS_CONFIG_PATH = '/config.js';

   /**
   * @var \Feecompass\Rankings\Model\Config
   */
    private $feecompassConfig;

    private $tokenService;

    public function __construct(Context $context, FeecompassConfig $feecompassConfig)
    {        
        $this->_storeManager = $context->getStoreManager();
        $this->scopeConfig = $context->getScopeConfig();
        $this->feecompassConfig = $feecompassConfig;
        $this->tokenService = new TokenService($feecompassConfig);
        parent::__construct($context);
    }

    public function getBaseUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl();
    }

    public function getToken()
    {
        return $this->tokenService->getToken();
    }

    public function getFeecompassBaseUrl()
    {
        return $this->feecompassConfig->getFeecompassApiUrl("");
    }

    public function getClientUrl()
    {
        return $this->getFeecompassBaseUrl() . self::FEECOMPASS_PROFILE_PATH;
    }

    public function getEcommerceSystem()
    {
        return "FCMagento2V1";
    }

    public function getProfile()
    {
        return "";
    }

    public function getConfigUrl()
    {
        $profileParam = "profile=" . urlencode($this-> getProfile());
        $ecommerceParam = "ecommerce=" . urlencode($this-> getEcommerceSystem());
        $tokenParam = "token=" . urlencode($this->getToken());
        $baseurlParam = "baseurl=" . urlencode($this->getFeecompassBaseUrl());
        $url = $this->getFeecompassBaseUrl() . self::FEECOMPASS_CONFIG_PATH . "?" . $profileParam . "&" . $ecommerceParam . "&" . $tokenParam . "&" . $baseurlParam;
        return $url;
    }

    public function isActivated()
    {
        return $this->feecompassConfig->isActivated();
    }
}