<?php

namespace Yosmy\Payment\Gateway\Authorize;

use Yosmy\Payment\Gateway;
use LogicException;

/**
 * @di\service()
 */
class TestCredentials
{
    /**
     * @var ExecuteRequest
     */
    private $executeRequest;

    /**
     * @param ExecuteRequest $executeRequest
     */
    public function __construct(
        ExecuteRequest $executeRequest
    ) {
        $this->executeRequest = $executeRequest;
    }

    /**
     * {@inheritDoc}
     */
    public function test() {
        try {
            $response = $this->executeRequest->execute(
                'authenticateTestRequest',
                []
            );

            return new Gateway\Customer(
                $response['customerProfileId']
            );
        } catch (Gateway\ApiException $e) {
            throw new LogicException(null, null, $e);
        }
    }
}