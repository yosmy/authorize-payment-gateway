<?php

namespace Yosmy\Payment\Gateway\Authorize;

use Yosmy\Payment\Gateway;
use Yosmy\ReportError;

/**
 * @di\service({
 *     tags: ['yosmy.payment.gateway.execute_charge']
 * })
 */
class ExecuteCharge implements Gateway\ExecuteCharge
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
     * @var Gateway\ProcessApiException[]
     */
    private $processExceptionServices;

    /**
     * @di\arguments({
     *     processExceptionServices: '#yosmy.payment.gateway.authorize.execute_charge.exception_throwed',
     * })
     *
     * @param ExecuteRequest                $executeRequest
     * @param ReportError                   $reportError
     * @param Gateway\ProcessApiException[] $processExceptionServices
     */
    public function __construct(
        ExecuteRequest $executeRequest,
        ReportError $reportError,
        ?array $processExceptionServices
    ) {
        $this->executeRequest = $executeRequest;
        $this->reportError = $reportError;
        $this->processExceptionServices = $processExceptionServices;
    }

    /**
     * {@inheritDoc}
     */
    public function execute(
        string $customer,
        string $card,
        int $amount,
        string $description,
        string $statement
    ) {
        $amount = number_format($amount / 100, 2, '.', '');

        try {
            $response = $this->executeRequest->execute(
                'createTransactionRequest',
                [
                    'transactionRequest' => [
                        'transactionType' => 'authCaptureTransaction',
                        'amount' => $amount,
                        'profile' => [
                            'customerProfileId' => $customer,
                            'paymentProfile' => [
                                'paymentProfileId' => $card
                            ]
                        ]
                    ]
                ]
            );

            return new Gateway\Charge(
                $response['transactionResponse']['transId'],
                time()
            );
        } catch (Gateway\ApiException $e) {
            foreach ($this->processExceptionServices as $service) {
                try {
                    $service->process($e);
                } catch (Gateway\FundsException|Gateway\IssuerException|Gateway\RiskException|Gateway\FraudException $e) {
                    throw $e;
                } catch (Gateway\FieldException $e) {
                    $this->reportError->report($e);

                    throw new Gateway\UnknownException();
                }
            }

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
