<?php
namespace Feecompass\Rankings\Model\Logger;

use Monolog\Logger;

class FeecompassHandler extends \Magento\Framework\Logger\Handler\Base
{
    /**
     * Logging level
     * @var int
     */
    protected $loggerType = Logger::DEBUG;

    /**
     * File name
     * @var string
     */
    protected $fileName = '/var/log/feecompass_rankings.log';
}
