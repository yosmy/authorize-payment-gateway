<?php

namespace Yosmy\Payment\Gateway\Authorize;

use Yosmy\Payment\Gateway;

/**
 * @di\service({
 *     tags: [
 *         'yosmy.payment.gateway.authorize.add_card.exception_throwed'
 *     ]
 * })
 */
class ProcessFieldApiException implements Gateway\ProcessApiException
{
    /**
     * {@inheritDoc}
     */
    public function process(Gateway\ApiException $e)
    {
        foreach ($e->getResponse()['messages']['message'] as $message) {
            if ($message['code'] == 'E00013') {
                switch ($message['text']) {
                    case 'Expiration Date is invalid.':
                        $field = 'month';

                        break;
                    default:
                        return;
                }

                throw new Gateway\FieldException($field);
            }
        }
    }
}