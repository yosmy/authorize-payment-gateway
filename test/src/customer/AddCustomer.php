<?php

namespace Yosmy\Payment\Gateway\Authorize\Test;

use Yosmy\Payment\Gateway;
use Yosmy\Payment\Gateway\Authorize;
use LogicException;

/**
 * @di\service()
 */
class AddCustomer
{
    /**
     * @var Authorize\AddCustomer
     */
    private $addCustomer;

    /**
     * @param Authorize\AddCustomer $addCustomer
     */
    public function __construct(Authorize\AddCustomer $addCustomer)
    {
        $this->addCustomer = $addCustomer;
    }

    /**
     * @cli\resolution({command: "/payment/gateway/authorize/add-customer"})
     *
     * @return Gateway\Customer
     */
    public function add() {
        try {
            return $this->addCustomer->add();
        } catch (Gateway\ApiException $e) {
            throw new LogicException(null, null, $e);
        }
    }
}