<?php

namespace Beanstream;

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
     * @var string $endpoints
     */
    protected $endpoints;

    /**
     * HttpConnector object
     *
     * @var \Beanstream\HttpConnector   $httpConnector
     */
    protected $httpConnector;

    /**
     * Constructor
     *
     * Inits the appropriate endpoint and httpconnector objects
     * Sets all of the Reporting class properties
     *
     * @param \Beanstream\Configuration $config
     */
    public function __construct(Configuration $config)
    {
        // Init endpoint
        $this->endpoints = new Endpoints($config->getPlatform(), $config->getApiVersion());

        // Init http connector
        $this->httpConnector = new HttpConnector(base64_encode($config->getMerchantId().':'.$config->getApiKey()));
    }

    /**
     * getTransactions() function - Get transactions result array based on search criteria
     * @link http://developer.beanstream.com/analyze-payments/search-specific-criteria/
     *
     * @param array $data search criteria
     * @return array Result Transactions
     */
    public function getTransactions($data)
    {
        // Get reporting endpoint
        $endpoint =  $this->endpoints->getReportingURL();

        // Process as is
        $result = $this->httpConnector->processTransaction('POST', $endpoint, $data);

        // Send back the result
        return $result;
    }

    /**
     * getTransaction() function - get a single transaction via 'Search'
     *  // TODO not exactly working, returning call help desk, but incoming payload seems ok
     * @link http://developer.beanstream.com/documentation/analyze-payments/
     *
     * @param string $transaction_id Transaction Id
     * @return array Transaction data
     */
    public function getTransaction($transaction_id = '')
    {
        // Get reporting endpoint
        $endpoint =  $this->endpoints->getPaymentUrl($transaction_id);

        // Process as is
        $result = $this->httpConnector->processTransaction('GET', $endpoint, null);

        // Send back the result
        return $result;
    }
}
