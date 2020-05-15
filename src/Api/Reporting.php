<?php

namespace NexusMerchants\Bambora\Api;

use NexusMerchants\Bambora\Communications\Endpoints;
use NexusMerchants\Bambora\Communications\HttpConnector;
use NexusMerchants\Bambora\Configuration;

/**
 * Reporting class to handle reports generation
 *
 * @author Kevin Saliba
 */
class Reporting
{
    /**
     * Reporting Endpoint object
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
     * Constructor
     *
     * Inits the appropriate endpoint and httpconnector objects
     * Sets all of the Reporting class properties
     *
     * @param \NexusMerchants\Bambora\Configuration $config
     */
    public function __construct(Configuration $config)
    {
        $this->endpoint = new Endpoints($config->getPlatform(), $config->getApiVersion());
        $this->connector = new HttpConnector(base64_encode($config->getMerchantId() . ':' . $config->getApiKey()));
    }

    /**
     * getTransactions() function - Get transactions result array based on search criteria
     * @link http://developer.beanstream.com/analyze-payments/search-specific-criteria/
     *
     * @param array $data search criteria
     *
     * @return array Result Transactions
     */
    public function getTransactions($data)
    {
        $endpoint = $this->endpoint->getReportingURL();
        $result = $this->connector->processTransaction('POST', $endpoint, $data);

        return $result;
    }

    /**
     * getTransaction() function - get a single transaction via 'Search'
     *    //TODO not exactly working, returning call help desk, but incoming payload seems ok
     * @link http://developer.beanstream.com/documentation/analyze-payments/
     *
     * @param string $transaction_id Transaction Id
     *
     * @return array Transaction data
     */
    public function getTransaction($transaction_id = '')
    {
        $endpoint = $this->endpoint->getPaymentUrl($transaction_id);
        $result = $this->connector->processTransaction('GET', $endpoint, null);

        return $result;
    }
}
