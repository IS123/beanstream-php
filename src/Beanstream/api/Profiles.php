<?php

namespace Beanstream;

/**
 * Profiles class to handle profile and card actions
 *
 * @author Kevin Saliba
 */
class Profiles
{
    /**
     * Profiles Endpoint object
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
     * Sets all of the Profiles class properties
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
     * createProfile() function - Create a new profile
     * @link http://developer.beanstream.com/documentation/tokenize-payments/create-new-profile/
     *
     * @param array $data Profile data
     * @return string Profile Id (aka customer_code)
     */
    public function createProfile($data = null)
    {
        // Get profiles endpoint
        $endpoint = $this->endpoints->getProfilesURL();

        // Process as is
        $result = $this->httpConnector->processTransaction('POST', $endpoint, $data);

        // Send back the new customer code
        return $result['customer_code'];
    }

    /**
     * getProfile() function - Retrieve a profile
     * @link http://developer.beanstream.com/documentation/tokenize-payments/retrieve-profile/
     *
     * @param string $profile_id Profile Id
     * @return array Profile data
     */
    public function getProfile($profile_id)
    {
        // Get this profile's endpoint
        $endpoint = $this->endpoints->getProfileURI($profile_id);

        // Process as is
        $result = $this->httpConnector->processTransaction('GET', $endpoint, null);

        // Unset($result['code'], $result['message']); // Not sure why this was being done.. why not give it all back?

        return $result;
    }

    /**
     * updateProfile() function - Update a profile via PUT
     * @link http://developer.beanstream.com/documentation/tokenize-payments/update-profile/
     *
     * @param string $profile_id Profile Id
     * @param array $data Profile data
     * @return bool TRUE
     */
    public function updateProfile($profile_id, $data = null)
    {
        // Get this profile's endpoint
        $endpoint = $this->endpoints->getProfileURI($profile_id);

        // Process as PUT
        $result = $this->httpConnector->processTransaction('PUT', $endpoint, $data);

        return true;
    }

    /**
     * deleteProfile() function - Delete a profile via DELETE http method
     * @link http://developer.beanstream.com/documentation/tokenize-payments/delete-profile/
     *
     * @param string $profile_id Profile Id
     * @return bool TRUE
     */
    public function deleteProfile($profile_id)
    {
        // Get this profile's endpoint
        $endpoint = $this->endpoints->getProfileURI($profile_id);

        // Process as DELETE
        $result = $this->httpConnector->processTransaction('DELETE', $endpoint, null);

        return true;
    }

    /**
     * getCards() function - Retrieve all cards in a profile
     * @link http://developer.beanstream.com/documentation/tokenize-payments/retrieve-cards-profile/
     *
     * @param string $profile_id Profile Id
     * @return array Cards data
     */
    public function getCards($profile_id)
    {
        // Get this profile's cards endpoint
        $endpoint = $this->endpoints->getCardsURI($profile_id);

        // Process as is
        $result = $this->httpConnector->processTransaction('GET', $endpoint, null);

        // Return cards
        return $result;
    }

    /**
     * addCard() function - Add a card to a profile
     * @link http://developer.beanstream.com/documentation/tokenize-payments/add-card-profile/
     *
     * @param string $profile_id Profile Id
     * @param array $data Card data
     * @return bool TRUE see note below
     */
    public function addCard($profile_id, $data)
    {
        // Get profiles cards endpoint
        $endpoint = $this->endpoints->getCardsURI($profile_id);

        // Process as is
        $result = $this->httpConnector->processTransaction('POST', $endpoint, $data);

        /*
         * XXX it would be more appropriate to return newly added card_id,
         * but API does not return it in result
         */
        return true;
    }

    /**
     * updateCard() function - Update a single card in a profile
     * @link http://developer.beanstream.com/documentation/tokenize-payments/update-card-profile/
     *
     * @param string $profile_id Profile Id
     * @param string $card_id Card Id
     * @param array $data Card data
     *
     * @return array Result
     */
    public function updateCard($profile_id, $card_id, $data)
    {
        // Get this card's endpoint
        $endpoint = $this->endpoints->getCardURI($profile_id, $card_id);

        // Process as is
        $result = $this->httpConnector->processTransaction('PUT', $endpoint, $data);

        return $result;
    }

    /**
     * deleteCard() function - Delete a card from a profile via DELETE http method
     * @link http://developer.beanstream.com/documentation/tokenize-payments/delete-card-profile/
     *
     * @param string $profile_id Profile Id
     * @param string $card_id Card Id
     * @return bool TRUE
     */
    public function deleteCard($profile_id, $card_id)
    {
        // Get this card's endpoint
        $endpoint = $this->endpoints->getCardURI($profile_id, $card_id);

        // Process as DELETE
        $result = $this->httpConnector->processTransaction('DELETE', $endpoint, null);

        return true;
    }
}
