<?php

namespace NexusMerchants\Bambora\Api;

use NexusMerchants\Bambora\Communications\Endpoints;
use NexusMerchants\Bambora\Communications\HttpConnector;
use NexusMerchants\Bambora\Configuration;
use NexusMerchants\Bambora\Exceptions\ApiException;

/**
 * Payments class to handle payment actions
 *
 * @author Kevin Saliba
 */
class Payments
{
    /**
     * Payments Endpoint object
     *
     * @var string $endpoint
     */
    protected $endpoint;

    /**
     * HttpConnector object
     *
     * @var    \NexusMerchants\Bambora\Communications\HttpConnector $connector
     */
    protected $connector;

    /**
     *  Merchant ID holder (used for unreferenced return only)
     *
     * @var string $merchantId
     */
    protected $merchantId;

    /**
     * Constructor
     *
     * Inits the appropriate endpoint and httpconnector objects
     * Sets all of the Payments class properties
     *
     * @param \NexusMerchants\Bambora\Configuration $config
     */
    public function __construct(Configuration $config)
    {
        $this->endpoint = new Endpoints($config->getPlatform(), $config->getApiVersion());
        $this->connector = new HttpConnector(base64_encode($config->getMerchantId() . ':' . $config->getApiKey()));
        $this->merchantId = $config->getMerchantId();
    }

    /**
     * makePayment() function - generic payment (no payment_method forced), processed as is
     * @link http://developer.beanstream.com/take-payments/
     *
     * @param array $data Order data
     *
     * @return array Transaction details
     */
    public function makePayment($data = null)
    {
        $endpoint = $this->endpoint->getBasePaymentsURL();

        return $this->connector->processTransaction('POST', $endpoint, $data);
    }

    /**
     * makeCardPayment() function - Card payment forced
     * @link http://developer.beanstream.com/documentation/take-payments/purchases/card/
     *
     * @param array $data Order data
     * @param bool $complete Set to false for pre-auth, default to TRUE
     *
     * @return array Transaction details
     */
    public function makeCardPayment($data = null, $complete = true)
    {
        //build endpoint
        $endpoint = $this->endpoint->getBasePaymentsURL();

        //force card
        $data['payment_method'] = 'card';
        //set completion
        $data['card']['complete'] = (is_bool($complete) === true ? $complete : true);

        //process card payment
        return $this->connector->processTransaction('POST', $endpoint, $data);
    }

    /**
     * continuePayment() function - Complete an Interac Online transaction
     * @link http://developer.beanstream.com/documentation/take-payments/purchases/interac-purchases/
     *
     * @param array $data Order data
     * @param bool $merchant_data The IDEBIT_MERCHDATA value returned by the Interac response
     *
     * @return array Transaction details
     */
    public function continuePayment($data = null, $merchant_data)
    {
        $endpoint = $this->endpoint->getContinuationsURL($merchant_data);
        return $this->connector->processTransaction('POST', $endpoint, $data);
    }

    /**
     * complete() function - Pre-authorization completion
     * @link http://developer.beanstream.com/documentation/take-payments/pre-authorization-completion/
     *
     * @param string $transaction_id Transaction Id
     * @param mixed $amount Order amount
     * @param string $order_number
     *
     * @return array Transaction details
     */
    public function complete($transaction_id, $amount, $order_number = null)
    {
        //get endpoint for this tid
        $endpoint = $this->endpoint->getPreAuthCompletionsURL($transaction_id);

        //force complete to true
        $data['card']['complete'] = true;

        //set amount
        $data['amount'] = $amount;

        //set order number if received
        if (!is_null($order_number)) {
            $data['order_number'] = $order_number;
        }

        //process completion (PAC)
        return $this->connector->processTransaction('POST', $endpoint, $data);
    }

    /**
     * makeCashPayment() function - Cash payment forced
     * @link http://developer.beanstream.com/documentation/take-payments/purchases/cash/
     *
     * @param array $data Order data
     *
     * @return array Transaction details
     */
    public function makeCashPayment($data = null)
    {
        //get endpoint
        $endpoint = $this->endpoint->getBasePaymentsURL();

        //force cash
        $data['payment_method'] = 'cash';

        //process cash payment
        return $this->connector->processTransaction('POST', $endpoint, $data);
    }

    /**
     * makeChequePayment() function - Cheque payment forced
     * @link http://developer.beanstream.com/documentation/take-payments/purchases/cheque-purchases/
     *
     * @param array $data Order data
     *
     * @return array Transaction details
     */
    public function makeChequePayment($data = null)
    {
        //get endpoint
        $endpoint = $this->endpoint->getBasePaymentsURL();

        //force chq
        $data['payment_method'] = 'cheque';

        //process chq payment
        return $this->connector->processTransaction('POST', $endpoint, $data);
    }

    /**
     * returnPayment() function (aka refund, can't use reserved 'return' keyword for method name)
     * @link http://developer.beanstream.com/documentation/take-payments/return/
     *
     * @param string $transaction_id Transaction Id
     * @param mixed $amount Order amount to return
     * @param string $order_number for the return
     *
     * @return array Transaction details
     */
    public function returnPayment($transaction_id, $amount, $order_number = null)
    {
        //get endpoint
        $endpoint = $this->endpoint->getReturnsURL($transaction_id);

        //set amount
        $data['amount'] = $amount;

        //set order number if received
        if (!is_null($order_number)) {
            $data['order_number'] = $order_number;
        }

        //process return
        return $this->connector->processTransaction('POST', $endpoint, $data);
    }

    /**
     * unreferencedReturn() function (aka unreferenced refund)
     * @link http://developer.beanstream.com/documentation/take-payments/unreferenced-return/
     *
     * @param array $data Return data (card or swipe)
     *
     * @return array Transaction details
     */
    public function unreferencedReturn($data)
    {
        //get endpoint
        $endpoint = $this->endpoint->getUnreferencedReturnsURL();

        //set merchant id (not sure why it's only needed here)
        $data['merchant_id'] = $this->merchantId;


        //process unreferenced return as is(could be card or swipe)
        return $this->connector->processTransaction('POST', $endpoint, $data);
    }

    /**
     * voidPayment() function (aka cancel)
     * @link http://developer.beanstream.com/documentation/take-payments/voids/
     *
     * @param string $transaction_id Transaction Id
     * @param mixed $amount Order amount
     *
     * @return array Transaction details
     */
    public function voidPayment($transaction_id, $amount)
    {
        //get endpoint
        $endpoint = $this->endpoint->getVoidsURL($transaction_id);

        //set amount
        $data['amount'] = $amount;

        //process void
        return $this->connector->processTransaction('POST', $endpoint, $data);
    }

    /**
     * makeProfilePayment() function - Take a payment via a profile
     * @link http://developer.beanstream.com/documentation/tokenize-payments/take-payment-profiles/
     *
     * @param string $profile_id Profile Id
     * @param int $card_id Card Id
     * @param array $data Order data
     * @param bool $complete Set to false for pre-auth, default to TRUE
     *
     * @return array Transaction details
     */
    public function makeProfilePayment($profile_id, $card_id, $data, $complete = true)
    {
        //get endpoint
        $endpoint = $this->endpoint->getBasePaymentsURL();

        //force profile
        $data['payment_method'] = 'payment_profile';

        //set profile array vars
        $data['payment_profile'] = array(
            'complete' => (is_bool($complete) === true ? $complete : true),
            'customer_code' => $profile_id,
            'card_id' => '' . $card_id,
        );

        //process payment via profile
        return $this->connector->processTransaction('POST', $endpoint, $data);
    }

    /**
     * Create a credit card single-use token
     *
     * @link https://dev.na.bambora.com/docs/guides/merchant_quickstart/calling_APIs/
     *
     * @param array $cardData
     *
     * @return string Legato token
     * @throws \NexusMerchants\Bambora\Exceptions\ApiException
     */
    public function getCardToken(array $cardData)
    {
        $result = $this->connector->processTransaction('POST', $this->endpoint->getTokenURL(), $cardData);

        if (!isset($result['token'])) { //no token received
            throw new ApiException('No Token Received', 0);
        }

        return $result['token'];
    }

    /**
     * Make payemnt with a single-use token
     * @link https://dev.na.bambora.com/docs/guides/merchant_quickstart/calling_APIs/
     *
     * @param string $token Legato token
     * @param array $data Order data
     * @param bool $complete Set to false for pre-auth, default to TRUE
     *
     * @return array Transaction details
     */
    public function makeTokenPayment($token, $data = null, $complete = true)
    {
        $data['payment_method'] = 'token';
        $data['token']['code'] = $token;
        $data['token']['name'] = $data['billing']['name'] ?? '';
        $data['token']['complete'] = $complete;

        return $this->connector->processTransaction('POST', $this->endpoint->getBasePaymentsURL(), $data);
    }
}
