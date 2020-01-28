<?php

namespace Yosmy\Payment\Gateway\Authorize\Test;

use Yosmy\Payment\Gateway;
use Yosmy\Payment\Gateway\Authorize;
use LogicException;

/**
 * @di\service()
 */
class RefundCharge
{
    /**
     * @var Authorize\RefundCharge
     */
    private $refundCharge;

    /**
     * @param Authorize\RefundCharge $refundCharge
     */
    public function __construct(Authorize\RefundCharge $refundCharge)
    {
        $this->refundCharge = $refundCharge;
    }

    /**
     * @cli\resolution({command: "/payment/gateway/authorize/refund-charge"})
     *
     * @param string $id
     */
    public function delete(
        string $id
    ) {
        try {
            $this->refundCharge->refund(
                $id
            );
        } catch (Gateway\ApiException $e) {
            throw new LogicException(null, null, $e);
        }
    }
}