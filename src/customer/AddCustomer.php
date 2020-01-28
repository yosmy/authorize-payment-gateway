<?php

namespace Yosmy\Payment\Gateway\Authorize;

use Yosmy\Payment\Gateway;
use Yosmy\ReportError;

/**
 * @di\service({
 *     tags: ['yosmy.payment.gateway.add_customer']
 * })
 */
class AddCustomer implements Gateway\AddCustomer
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
    public function add()
    {
        $id = uniqid();

        try {
            $response = $this->executeRequest->execute(
                'createCustomerProfileRequest',
                [
                    'profile' => [
                        'merchantCustomerId' => $id
                    ]
                ]
            );

            return new Gateway\Customer(
                $response['customerProfileId']
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