<?php

namespace Yosmy\Payment\Gateway\Authorize;

use Yosmy\Payment\Gateway;
use Yosmy\ReportError;

/**
 * @di\service({
 *     tags: ['yosmy.payment.gateway.add_card']
 * })
 */
class AddCard implements Gateway\AddCard
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
     *     processExceptionServices: '#yosmy.payment.gateway.authorize.add_card.exception_throwed',
     * })
     *
     * @param ExecuteRequest                $executeRequest
     * @param ReportError                   $reportError
     * @param Gateway\ProcessApiException[] $processExceptionServices
     */
    public function __construct(
        ExecuteRequest $executeRequest,
        ReportError $reportError,
        array $processExceptionServices
    ) {
        $this->executeRequest = $executeRequest;
        $this->reportError = $reportError;
        $this->processExceptionServices = $processExceptionServices;
    }

    /**
     * {@inheritDoc}
     */
    public function add(
        string $customer,
        string $name,
        string $number,
        string $month,
        string $year,
        string $cvc,
        string $zip
    ) {
        $names = explode(' ', $name);

        if (!isset($names[1])) {
            $names[1] = '';
        }

        try {
            $response = $this->executeRequest->execute(
                'createCustomerPaymentProfileRequest',
                [
                    'customerProfileId' => $customer,
                    'paymentProfile' => [
                        'billTo' => [
                            'firstName' => $names[0],
                            'lastName' => $names[1],
                            'zip' => $zip
                        ],
                        'payment' => [
                            'creditCard' => [
                                'cardNumber' => $number,
                                'expirationDate' => sprintf('20%s-%s', $year, $month)
                            ]
                        ]
                    ]
                ]
            );
        } catch (Gateway\ApiException $e) {
            foreach ($this->processExceptionServices as $service) {
                try {
                    $service->process($e);
                } catch (Gateway\FieldException|Gateway\IssuerException|Gateway\RiskException|Gateway\FraudException $e) {
                    throw $e;
                } catch (Gateway\FundsException $e) {
                    $this->reportError->report($e);

                    throw new Gateway\UnknownException();
                }
            }

            $this->reportError->report($e);

            throw new Gateway\UnknownException();
        }

        $last4 = substr($number, -4);

        return new Gateway\Card(
            $response['customerPaymentProfileId'],
            $last4
        );
    }

    /**
     * {@inheritDoc}
     */
    public function identify() {
        return 'authorize';
    }
}