<?php

declare(strict_types=1);

namespace Feecompass\Rankings\Helper;
use \Feecompass\Rankings\Model\Config as FeecompassConfig;
use Ahc\Jwt\JWT;

/**
 * Class TokenService
 */
class TokenService
{

    private $jwt;
    private $clientId;
    private $secret;

    function __construct(FeecompassConfig $feecompassConfig) {
        $this->clientId = $feecompassConfig->getAppKey();
        $this->secret = $feecompassConfig->getSecret();
        $this->jwt = new JWT([$this->clientId => $this->secret], 'HS256', 7*24*3600, 10);
    }

    function getToken() {
        $tokenString = $this->jwt->encode(['clientid' => $this->clientId], ['kid' => $this->clientId]);
        return $tokenString;
    }

}
