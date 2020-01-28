<?php

namespace Yosmy\Payment\Gateway\Authorize;

use Yosmy\Http;
use Yosmy\Payment\Gateway;

/**
 * @di\service({
 *     private: true
 * })
 */
class ExecuteRequest
{
    const METHOD_POST = 'post';

    /**
     * @var string
     */
    private $endpoint;

    /**
     * @var string
     */
    private $apiLoginId;

    /**
     * @var string
     */
    private $transactionKey;

    /**
     * @var Http\ExecuteRequest
     */
    private $executeRequest;

    /**
     * @var Request\LogEvent
     */
    private $logEvent;

    /**
     * @di\arguments({
     *     endpoint:       "%authorize_endpoint%",
     *     apiLoginId:     "%authorize_api_login_id%",
     *     transactionKey: "%authorize_transaction_key%",
     * })
     *
     * @param string              $endpoint
     * @param string              $apiLoginId
     * @param string              $transactionKey
     * @param Http\ExecuteRequest $executeRequest
     * @param Request\LogEvent    $logEvent
     */
    public function __construct(
        string $endpoint,
        string $apiLoginId,
        string $transactionKey,
        Http\ExecuteRequest $executeRequest,
        Request\LogEvent $logEvent
    ) {
        $this->endpoint = $endpoint;
        $this->apiLoginId = $apiLoginId;
        $this->transactionKey = $transactionKey;
        $this->executeRequest = $executeRequest;
        $this->logEvent = $logEvent;
    }

    /**
     * @param string $key
     * @param array  $params
     *
     * @return array
     *
     * @throws Gateway\ApiException
     */
    public function execute(
        string $key,
        array $params = []
    ) {
        $request = [
            'params' => [
                $key => $params
            ]
        ];

        $params = [
            $key => array_merge(
                [
                    'merchantAuthentication' => [
                        'name' => $this->apiLoginId,
                        'transactionKey' => $this->transactionKey,
                    ]
                ],
                $params
            )
        ];

        try {
            $response = $this->executeRequest->execute(
                self::METHOD_POST,
                sprintf('%s/xml/v1/request.api', $this->endpoint),
                [
                    'json' => $params
                ]
            );

            $response = $response->getRawBody();

            // Remove BOM character
            $response = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $response);

            $response = json_decode($response, true);

            $this->logEvent->log(
                $request,
                $response
            );

            if ($response['messages']['resultCode'] == 'Error') {
                throw new Gateway\ApiException($response);
            }

            return $response;
        } catch (Http\Exception $e) {
            $response = $e->getResponse();

            $this->logEvent->log(
                $request,
                $response
            );

            throw new Gateway\ApiException($response);
        }
    }
}