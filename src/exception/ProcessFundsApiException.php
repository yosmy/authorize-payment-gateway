<?php

namespace Yosmy\Payment\Gateway\Authorize;

use Yosmy\Payment\Gateway;

/**
 * @di\service({
 *     tags: [
 *         'yosmy.payment.gateway.authorize.execute_charge.exception_throwed'
 *     ]
 * })
 */
class ProcessFundsApiException implements Gateway\ProcessApiException
{
    /**
     * {@inheritDoc}
     */
    public function process(Gateway\ApiException $e)
    {

    }
}