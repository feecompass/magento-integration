<?php

namespace Feecompass\Rankings\Block\Adminhtml\System\Config;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Feecompass\Rankings\Model\Config as FeecompassConfig;

class ModuleVersion extends Field
{
    /**
     * Template path
     *
     * @var string
     */
    protected $_template = 'Feecompass_Rankings::system/config/module_version.phtml';

    /**
     * @var FeecompassConfig
     */
    private $feecompassConfig;

    /**
     * @param  Context     $context
     * @param  FeecompassConfig $feecompassConfig
     * @param  array       $data
     */
    public function __construct(
        Context $context,
        FeecompassConfig $feecompassConfig,
        array $data = []
    ) {
        $this->feecompassConfig = $feecompassConfig;
        parent::__construct($context, $data);
    }

    /**
     * Remove scope label
     *
     * @param  AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element)
    {
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
    }

    /**
     * Return element html
     *
     * @param  AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        return $this->_toHtml();
    }

    /**
     * Generate module version
     *
     * @return string
     */
    public function getModuleVersion()
    {
        return $this->feecompassConfig->getModuleVersion();
    }
}
