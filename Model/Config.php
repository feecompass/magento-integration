<?php

namespace Feecompass\Rankings\Model;

use Magento\Config\Model\ResourceModel\Config as ResourceConfig;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\Module\ModuleListInterface;
use Magento\Framework\Stdlib\DateTime\DateTimeFactory;
use Magento\Framework\UrlInterface;
use Magento\Sales\Model\Order;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;
use Feecompass\Rankings\Model\Logger as FeecompassLogger;

class Config
{
    const MODULE_NAME = 'Feecompass_Rankings';

    //= General Settings
    const XML_PATH_FEECOMPASS_ALL = 'feecompass';
    const XML_PATH_FEECOMPASS_ENABLED = 'feecompass/settings/active';
    const XML_PATH_FEECOMPASS_APP_KEY = 'feecompass/settings/app_key';
    const XML_PATH_FEECOMPASS_SECRET = 'feecompass/settings/secret';
    const XML_PATH_FEECOMPASS_DEBUG_MODE_ENABLED = 'feecompass/settings/debug_mode_active';
    const XML_PATH_FEECOMPASS_API_URL = 'feecompass/settings/feecompass_api_url';
    //= Not visible on system.xml
    const XML_PATH_FEECOMPASS_WIDGET_URL = 'feecompass/env/feecompass_widget_url';
    const XML_PATH_FEECOMPASS_MODULE_INFO_INSTALLATION_DATE = 'feecompass/module_info/feecompass_installation_date';

    private $allStoreIds = [0 => null, 1 => null];

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var ResourceConfig
     */
    private $resourceConfig;

    /**
     * @var EncryptorInterface
     */
    private $encryptor;

    /**
     * @var DateTimeFactory
     */
    private $datetimeFactory;

    /**
     * @var ModuleListInterface
     */
    private $moduleList;

    /**
     * @var ProductMetadataInterface
     */
    private $productMetadata;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var FeecompassLogger
     */
    private $feecompassLogger;

    /**
     * @method __construct
     * @param  StoreManagerInterface    $storeManager
     * @param  ScopeConfigInterface     $scopeConfig
     * @param  ResourceConfig           $resourceConfig
     * @param  EncryptorInterface       $encryptor
     * @param  DateTimeFactory          $datetimeFactory
     * @param  ModuleListInterface      $moduleList
     * @param  ProductMetadataInterface $productMetadata
     * @param  LoggerInterface          $logger
     * @param  FeecompassLogger              $feecompassLogger
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig,
        ResourceConfig $resourceConfig,
        EncryptorInterface $encryptor,
        DateTimeFactory $datetimeFactory,
        ModuleListInterface $moduleList,
        ProductMetadataInterface $productMetadata,
        LoggerInterface $logger,
        FeecompassLogger $feecompassLogger
    ) {
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
        $this->resourceConfig = $resourceConfig;
        $this->encryptor = $encryptor;
        $this->datetimeFactory = $datetimeFactory;
        $this->moduleList = $moduleList;
        $this->productMetadata = $productMetadata;
        $this->logger = $logger;
        $this->feecompassLogger = $feecompassLogger;
    }

    /**
     * @method getStoreManager
     * @return StoreManagerInterface
     */
    public function getStoreManager()
    {
        return $this->storeManager;
    }

    /**
     * @method isSingleStoreMode
     * @return bool
     */
    public function isSingleStoreMode()
    {
        return $this->storeManager->isSingleStoreMode();
    }

    /**
     * @method getWebsiteIdByStoreId
     * @param int $storeId
     * @return int
     */
    public function getWebsiteIdByStoreId($storeId)
    {
        return $this->storeManager->getStore($storeId)->getWebsiteId();
    }

    /**
     * @return mixed
     */
    public function getConfig($configPath, $scopeId = null, $scope = null)
    {
        if (!$scope && $this->isSingleStoreMode()) {
            return $this->scopeConfig->getValue($configPath);
        }
        $scopeId = ($scopeId === null) ? $this->getCurrentStoreId() : $scopeId;

        return $this->scopeConfig->getValue($configPath, $scope ?: ScopeInterface::SCOPE_STORE, $scopeId);
    }

    /**
     * @return boolean
     */
    public function isEnabled($scopeId = null, $scope = null)
    {
        return ($this->getConfig(self::XML_PATH_FEECOMPASS_ENABLED, $scopeId, $scope)) ? true : false;
    }

    /**
     * @return boolean
     */
    public function isDebugMode($scope = null, $scopeId = null)
    {
        return ($this->getConfig(self::XML_PATH_FEECOMPASS_DEBUG_MODE_ENABLED, $scope, $scopeId)) ? true : false;
    }

    /**
     * @return string
     */
    public function getAppKey($scopeId = null, $scope = null)
    {
        return $this->getConfig(self::XML_PATH_FEECOMPASS_APP_KEY, $scopeId, $scope);
    }


    /**
     * @return string
     */
    public function getSecret($scopeId = null, $scope = null)
    {
        return (($secret = $this->getConfig(self::XML_PATH_FEECOMPASS_SECRET, $scopeId, $scope))) ? $this->encryptor->decrypt($secret) : null;
    }

    /**
     * @method getModuleInstallationDate
     * @param  string                 $format
     * @return date
     */
    public function getModuleInstallationDate($format = 'Y-m-d')
    {
        $timestamp = strtotime($this->getConfig(self::XML_PATH_FEECOMPASS_MODULE_INFO_INSTALLATION_DATE) ?: $this->getCurrentDate());
        return date($format, $timestamp);
    }

    /**
     * @return boolean
     */
    public function isAppKeyAndSecretSet($scopeId = null, $scope = null)
    {
        return ($this->getAppKey($scopeId, $scope) && $this->getSecret($scopeId, $scope)) ? true : false;
    }

    /**
     * @return boolean
     */
    public function isActivated($scopeId = null, $scope = null)
    {
        return ($this->isEnabled($scopeId, $scope) && $this->isAppKeyAndSecretSet($scopeId, $scope)) ? true : false;
    }

    /**
     * @method getFeecompassApiUrl
     * @param  string $path
     * @return string
     */
    public function getFeecompassApiUrl($path = "")
    {
        $feecompassApiUrl = $this->getConfig(self::XML_PATH_FEECOMPASS_API_URL);
        return $feecompassApiUrl . $path;
        /* if(preg_match("/feecompass\.com\/$|feecompass\.xyz\/$/", $feecompassApiUrl)) {
        return $feecompassApiUrl . $path;
        } else {
            return "https://api.feecompass.com/" . $path;
        } */
    }

    /**
     * Log to system.log
     * @method log
     * @param  mixed  $message
     * @param  string $type
     * @param  array  $data
     * @return $this
     */
    public function log($message, $type = "debug", $data = [], $prefix = '[Feecompass Log] ')
    {
        if ($type !== 'debug' || $this->isDebugMode()) {
            if (!isset($data['store_id'])) {
                $data['store_id'] = $this->getCurrentStoreId();
            }
            if (!isset($data['app_key'])) {
                $data['app_key'] = $this->getAppKey();
            }
            switch ($type) {
                case 'error':
                    $this->logger->error($prefix . json_encode($message), $data);
                    break;
                case 'info':
                    $this->logger->info($prefix . json_encode($message), $data);
                    break;
                case 'debug':
                default:
                    $this->logger->debug($prefix . json_encode($message), $data);
                    break;
            }
            $this->feecompassLogger->info($prefix . json_encode($message), $data);
        }
        return $this;
    }

    /**
     * @method getCurrentDate
     * @return date
     */
    public function getCurrentDate()
    {
        return $this->datetimeFactory->create()->gmtDate();
    }

    /**
     * @method getCurrentStoreId
     * @return int
     */
    public function getCurrentStoreId()
    {
        return $this->storeManager->getStore()->getId();
    }

    public function getModuleVersion()
    {
        return $this->moduleList->getOne(self::MODULE_NAME)['setup_version'];
    }

    public function getMagentoPlatformName()
    {
        return $this->productMetadata->getName();
    }

    public function getMagentoPlatformEdition()
    {
        return $this->productMetadata->getEdition();
    }

    public function getMagentoPlatformVersion()
    {
        return $this->productMetadata->getVersion();
    }
}
