<?php

namespace Beanstream;

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
     * @param \Beanstream\Configuration $config
     */
    public function __construct(Configuration $config)
    {
        // Init endpoint
        $this->endpoints = new Endpoints($config->getPlatform(), $config->getApiVersion());

        // Init http connector
        $this->httpConnector = new HttpConnector(base64_encode($config->getMerchantId().':'.$config->getApiKey()));

        // Set merchant id from config (only needed for unreferenced return)
        $this->merchantId = $config->getMerchantId();
    }

    /**
     * makePayment() function - generic payment (no payment_method forced), processed as is
     * @link http://developer.beanstream.com/take-payments/
     *
     * @param array $data Order data
     * @return array Transaction details
     */
    public function makePayment($data = null)
    {
        // Build endpoint
        $endpoint = $this->endpoints->getBasePaymentsURL();

        // Process as is
        return $this->httpConnector->processTransaction('POST', $endpoint, $data);
    }

    /**
     * makeCardPayment() function - Card payment forced
     * @link http://developer.beanstream.com/documentation/take-payments/purchases/card/
     *
     * @param array $data Order data
     * @param bool $complete Set to false for pre-auth, default to TRUE
     * @return array Transaction details
     */
    public function makeCardPayment($data = null, $complete = true)
    {
        // Build endpoint
        $endpoint = $this->endpoints->getBasePaymentsURL();

        // Force card
        $data['payment_method'] = 'card';

        // Set completion
        $data['card']['complete'] = (is_bool($complete) === true ? $complete : true);

        // Process card payment
        return $this->httpConnector->processTransaction('POST', $endpoint, $data);
    }

    /**
     * continuePayment() function - Complete an Interac Online transaction
     * @link http://developer.beanstream.com/documentation/take-payments/purchases/interac-purchases/
     *
     * @param array $data Order data
     * @param bool $merchant_data The IDEBIT_MERCHDATA value returned by the Interac response
     * @return array Transaction details
     */
    public function continuePayment($data, $merchant_data)
    {
        $endpoint = $this->endpoints->getContinuationsURL($merchant_data);
        return $this->httpConnector->processTransaction('POST', $endpoint, $data);
    }

    /**
     * complete() function - Pre-authorization completion
     * @link http://developer.beanstream.com/documentation/take-payments/pre-authorization-completion/
     *
     * @param string $transaction_id Transaction Id
     * @param mixed $amount Order amount
     * @param string $order_number
     * @return array Transaction details
     */
    public function complete($transaction_id, $amount, $order_number = null)
    {

        // Get endpoint for this tid
        $endpoint = $this->endpoints->getPreAuthCompletionsURL($transaction_id);

        // Force complete to true
        $data['card']['complete'] = true;

        // Set amount
        $data['amount'] = $amount;

        // Set order number if received
        if (!is_null($order_number)) {
            $data['order_number'] = $order_number;
        }

        // Process completion (PAC)
        return $this->httpConnector->processTransaction('POST', $endpoint, $data);
    }

    /**
     * makeCashPayment() function - Cash payment forced
     * @link http://developer.beanstream.com/documentation/take-payments/purchases/cash/
     *
     * @param array $data Order data
     * @return array Transaction details
     */
    public function makeCashPayment($data = null)
    {
        // Get endpoint
        $endpoint = $this->endpoints->getBasePaymentsURL();

        // Force cash
        $data['payment_method'] = 'cash';

        // Process cash payment
        return $this->httpConnector->processTransaction('POST', $endpoint, $data);
    }

    /**
     * makeChequePayment() function - Cheque payment forced
     * @link http://developer.beanstream.com/documentation/take-payments/purchases/cheque-purchases/
     *
     * @param array $data Order data
     * @return array Transaction details
     */
    public function makeChequePayment($data = null)
    {
        // Get endpoint
        $endpoint = $this->endpoints->getBasePaymentsURL();

        // Force chq
        $data['payment_method'] = 'cheque';

        // Process chq payment
        return $this->httpConnector->processTransaction('POST', $endpoint, $data);
    }

    /**
     * returnPayment() function (aka refund, can't use reserved 'return' keyword for method name)
     * @link http://developer.beanstream.com/documentation/take-payments/return/
     *
     * @param string $transaction_id Transaction Id
     * @param mixed $amount Order amount to return
     * @param string $order_number for the return
     * @return array Transaction details
     */
    public function returnPayment($transaction_id, $amount, $order_number = null)
    {
        // Get endpoint
        $endpoint = $this->endpoints->getReturnsURL($transaction_id);

        // Set amount
        $data['amount'] = $amount;

        // Set order number if received
        if (!is_null($order_number)) {
            $data['order_number'] = $order_number;
        }

        // Process return
        return $this->httpConnector->processTransaction('POST', $endpoint, $data);
    }

    /**
     * unreferencedReturn() function (aka unreferenced refund)
     * @link http://developer.beanstream.com/documentation/take-payments/unreferenced-return/
     *
     * @param array $data Return data (card or swipe)
     * @return array Transaction details
     */
    public function unreferencedReturn($data)
    {
        // Get endpoint
        $endpoint = $this->endpoints->getUnreferencedReturnsURL();

        // Set merchant id (not sure why it's only needed here)
        $data['merchant_id'] = $this->merchantId;

        // Process unreferenced return as is(could be card or swipe)
        return $this->httpConnector->processTransaction('POST', $endpoint, $data);
    }

    /**
     * voidPayment() function (aka cancel)
     * @link http://developer.beanstream.com/documentation/take-payments/voids/
     *
     * @param string $transaction_id Transaction Id
     * @param mixed $amount Order amount
     * @return array Transaction details
     */
    public function voidPayment($transaction_id, $amount)
    {
        // Get endpoint
        $endpoint = $this->endpoints->getVoidsURL($transaction_id);

        // Set amount
        $data['amount'] = $amount;

        // Process void
        return $this->httpConnector->processTransaction('POST', $endpoint, $data);
    }

    /**
     * makeProfilePayment() function - Take a payment via a profile
     * @link http://developer.beanstream.com/documentation/tokenize-payments/take-payment-profiles/
     *
     * @param string $profile_id Profile Id
     * @param int $card_id Card Id
     * @param array $data Order data
     * @param bool $complete Set to false for pre-auth, default to TRUE
     * @return array Transaction details
     */
    public function makeProfilePayment($profile_id, $card_id, $data, $complete = true)
    {
        // Get endpoint
        $endpoint = $this->endpoints->getBasePaymentsURL();

        // Force profile
        $data['payment_method'] = 'payment_profile';

        // Set profile array vars
        $data['payment_profile'] = [
            'complete' => (is_bool($complete) === true ? $complete : true),
            'customer_code' => $profile_id,
            'card_id' => ''.$card_id,
        ];

        // Process payment via profile
        return $this->httpConnector->processTransaction('POST', $endpoint, $data);
    }

    /**
     * getTokenTest() function - obtains legato token (shouldn't be called ever but useful to have for testing)
     * @link http://developer.beanstream.com/documentation/legato/server-to-server-integration-by-api/
     *
     * @param array $data Order data
     *
     * @return string Legato token
     * @throws ApiException
     */
    public function getTokenTest($data = null)
    {
        // Get endpoint
        $endpoint = $this->endpoints->getTokenURL();

        // Force token
        $data['payment_method'] = 'token';

        // Get token result array
        $result = $this->httpConnector->processTransaction('POST', $endpoint, $data);

        // Check if we're good
        if (!isset($result['token'])) { // No token received
            throw new ApiException('No Token Received', 0);
        }

        // Return Legato token
        return $result['token'];
    }

    /**
     * makeLegatoTokenPayment() function - Take a payment via a profile
     * @link http://developer.beanstream.com/documentation/legato/server-to-server-integration-by-api/
     *
     * @param string $token Legato token
     * @param array $data Order data
     * @param bool $complete Set to false for pre-auth, default to TRUE
     * @return array Transaction details
     */
    public function makeLegatoTokenPayment($token, $data = null, $complete = true)
    {
        // Get endpoint
        $endpoint = $this->endpoints->getBasePaymentsURL();

        // Force token
        $data['payment_method'] = 'token';

        // Add token vars
        $data['token']['code'] = $token;
        $data['token']['name'] = (isset($data['name']) ? $data['name'] : '');
        $data['token']['complete'] = (is_bool($complete) === true ? $complete : true);

        // Process payment via Legato token
        return $this->httpConnector->processTransaction('POST', $endpoint, $data);
    }
}
