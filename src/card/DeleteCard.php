<?php

namespace Yosmy\Payment\Gateway\Authorize;

use Yosmy\Payment\Gateway;
use Yosmy\ReportError;

/**
 * @di\service({
 *     tags: ['yosmy.payment.gateway.delete_card']
 * })
 */
class DeleteCard implements Gateway\DeleteCard
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
    public function delete(
        string $customer,
        string $card
    ) {
        try {
            $this->executeRequest->execute(
                'deleteCustomerPaymentProfileRequest',
                [
                    'customerProfileId' => $customer,
                    'customerPaymentProfileId' => $card
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