<?php

namespace Yosmy\Payment\Gateway\Authorize;

use Yosmy\Payment\Gateway;
use Yosmy\ReportError;

/**
 * @di\service({
 *     tags: ['yosmy.payment.gateway.refund_charge']
 * })
 */
class RefundCharge implements Gateway\RefundCharge
{
    /**
     * @var ExecuteRequest
     */
    private $executeRequest;

    /**
     * @var ReportError
     */
    private $reportError;

    /**
     * @param ExecuteRequest $executeRequest
     * @param ReportError    $reportError
     */
    public function __construct(
        ExecuteRequest $executeRequest,
        ReportError $reportError
    ) {
        $this->executeRequest = $executeRequest;
        $this->reportError = $reportError;
    }

    /**
     * {@inheritDoc}
     */
    public function refund(
        string $id
    ) {
        try {
            $this->executeRequest->execute(
                'createTransactionRequest',
                [
                    'transactionRequest' => [
                        'transactionType' => 'voidTransaction',
                        'refTransId' => $id
                    ]
                ]
            );
        } catch (Gateway\ApiException $e) {
            $this->reportError->report($e);

            throw new Gateway\UnknownException();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function identify() {
        return 'authorize';
    }
}
