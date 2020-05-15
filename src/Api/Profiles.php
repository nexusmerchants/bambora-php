<?php

namespace NexusMerchants\Bambora\Api;

use NexusMerchants\Bambora\Communications\Endpoints;
use NexusMerchants\Bambora\Communications\HttpConnector;
use NexusMerchants\Bambora\Configuration;

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
     * Sets all of the Profiles class properties
     *
     * @param \NexusMerchants\Bambora\Configuration $config
     */
    public function __construct(Configuration $config)
    {
        $this->endpoint = new Endpoints($config->getPlatform(), $config->getApiVersion());
        $this->connector = new HttpConnector(base64_encode($config->getMerchantId() . ':' . $config->getApiKey()));
    }

    /**
     * createProfile() function - Create a new profile
     * @link http://developer.beanstream.com/documentation/tokenize-payments/create-new-profile/
     *
     * @param array $data Profile data
     *
     * @return string Profile Id (aka customer_code)
     */
    public function createProfile($data = null)
    {
        $endpoint = $this->endpoint->getProfilesURL();
        $result = $this->connector->processTransaction('POST', $endpoint, $data);

        return $result['customer_code'];
    }

    /**
     * getProfile() function - Retrieve a profile
     * @link http://developer.beanstream.com/documentation/tokenize-payments/retrieve-profile/
     *
     * @param string $profile_id Profile Id
     *
     * @return array Profile data
     */
    public function getProfile($profile_id)
    {
        $endpoint = $this->endpoint->getProfileURI($profile_id);
        $result = $this->connector->processTransaction('GET', $endpoint, null);

        return $result;
    }

    /**
     * updateProfile() function - Update a profile via PUT
     * @link http://developer.beanstream.com/documentation/tokenize-payments/update-profile/
     *
     * @param string $profile_id Profile Id
     * @param array $data Profile data
     *
     * @return bool TRUE
     */
    public function updateProfile($profile_id, $data = null)
    {
        $endpoint = $this->endpoint->getProfileURI($profile_id);
        $result = $this->connector->processTransaction('PUT', $endpoint, $data);

        return true;
    }

    /**
     * deleteProfile() function - Delete a profile via DELETE http method
     * @link http://developer.beanstream.com/documentation/tokenize-payments/delete-profile/
     *
     * @param string $profile_id Profile Id
     *
     * @return bool TRUE
     */
    public function deleteProfile($profile_id)
    {
        $endpoint = $this->endpoint->getProfileURI($profile_id);
        $result = $this->connector->processTransaction('DELETE', $endpoint, null);

        return true;
    }

    /**
     * getCards() function - Retrieve all cards in a profile
     * @link http://developer.beanstream.com/documentation/tokenize-payments/retrieve-cards-profile/
     *
     * @param string $profile_id Profile Id
     *
     * @return array Cards data
     */
    public function getCards($profile_id)
    {
        $endpoint = $this->endpoint->getCardsURI($profile_id);
        $result = $this->connector->processTransaction('GET', $endpoint, null);

        return $result;
    }

    /**
     * addCard() function - Add a card to a profile
     * @link http://developer.beanstream.com/documentation/tokenize-payments/add-card-profile/
     *
     * @param string $profile_id Profile Id
     * @param array $data Card data
     *
     * @return bool TRUE see note below
     */
    public function addCard($profile_id, $data)
    {
        $endpoint = $this->endpoint->getCardsURI($profile_id);
        $result = $this->connector->processTransaction('POST', $endpoint, $data);

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
        $endpoint = $this->endpoint->getCardURI($profile_id, $card_id);
        $result = $this->connector->processTransaction('PUT', $endpoint, $data);

        return $result;
    }

    /**
     * deleteCard() function - Delete a card from a profile via DELETE http method
     * @link http://developer.beanstream.com/documentation/tokenize-payments/delete-card-profile/
     *
     * @param string $profile_id Profile Id
     * @param string $card_id Card Id
     *
     * @return bool TRUE
     */
    public function deleteCard($profile_id, $card_id)
    {
        $endpoint = $this->endpoint->getCardURI($profile_id, $card_id);
        $result = $this->connector->processTransaction('DELETE', $endpoint, null);

        return true;
    }
}
