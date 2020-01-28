<?php

namespace Yosmy\Payment\Gateway\Authorize\Test;

use Yosmy\Payment\Gateway;
use Yosmy\Payment\Gateway\Authorize;
use LogicException;

/**
 * @di\service()
 */
class DeleteCard
{
    /**
     * @var Authorize\DeleteCard
     */
    private $deleteCard;

    /**
     * @param Authorize\DeleteCard $deleteCard
     */
    public function __construct(Authorize\DeleteCard $deleteCard)
    {
        $this->deleteCard = $deleteCard;
    }

    /**
     * @cli\resolution({command: "/payment/gateway/authorize/delete-card"})
     *
     * @param string $customer
     * @param string $card
     */
    public function delete(
        string $customer,
        string $card
    ) {
        try {
            $this->deleteCard->delete(
                $customer,
                $card
            );
        } catch (Gateway\ApiException $e) {
            throw new LogicException(null, null, $e);
        }
    }
}