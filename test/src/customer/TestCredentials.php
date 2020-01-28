<?php

namespace Yosmy\Payment\Gateway\Authorize\Test;

use Yosmy\Payment\Gateway;
use Yosmy\Payment\Gateway\Authorize;
use LogicException;

/**
 * @di\service()
 */
class TestCredentials
{
    /**
     * @var Authorize\TestCredentials
     */
    private $testCredentials;

    /**
     * @param Authorize\TestCredentials $testCredentials
     */
    public function __construct(Authorize\TestCredentials $testCredentials)
    {
        $this->testCredentials = $testCredentials;
    }

    /**
     * @cli\resolution({command: "/payment/gateway/authorize/test-credentials"})
     *
     * @return Gateway\Customer
     */
    public function test() {
        try {
            return $this->testCredentials->test();
        } catch (Gateway\ApiException $e) {
            throw new LogicException(null, null, $e);
        }
    }
}