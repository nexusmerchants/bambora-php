<?php

namespace NexusMerchants\Bambora;

use NexusMerchants\Bambora\Exceptions\ConfigurationException;

/**
 * Configuration class to handle merchant id, api keys, platform & version
 *
 * @author Kevin Saliba
 */
class Configuration
{
    /**
     * Configuration: API Version
     *
     * @var string $_version
     */
    protected $version = 'v1';

    /**
     * Configuration: API Platform
     *
     * @var string $_platform
     */
    protected $platform = 'api';

    /**
     * Configuration: Merchant ID
     *
     * @var string $_merchantId
     */
    protected $merchantId;

    /**
     * Configuration: API Key
     *
     * @var string $_apiKey
     */
    protected $apiKey;

    /**
     * setMerchantId() function
     *
     * @param string $merchantId
     *
     * @throws ConfigurationException
     */
    public function setMerchantId($merchantId = '')
    {
        //check to make sure string strlen is 9 containing only digits 0-9
        if (!preg_match('/^[0-9]{9}$/', $merchantId)) { //TODO switch to actual real assertmerchantId
            //throw exception
            throw new ConfigurationException('Invalid Merchant ID provided: ' . $merchantId . ' Expected 9 digits.');
        }
        $this->merchantId = $merchantId;
    }

    /**
     * getMerchantId() function
     *
     * @return string merchant id
     */
    public function getMerchantId()
    {
        return $this->merchantId;
    }

    /**
     * setApiKey() function
     *
     * @param string $apiKey
     *
     * @return void
     */
    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    /**
     * getApiKey() function
     *
     * @return string api key
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * setPlatform() function
     *
     * @param string $platform
     *
     * @return void
     */
    public function setPlatform($platform = null)
    {
        if (!empty($platform)) {
            $this->platform = $platform;
        }
    }

    /**
     * getPlatform() function
     *
     * @return string platform
     */
    public function getPlatform()
    {
        return $this->platform;
    }

    /**
     * setApiVersion() function
     *
     * @param string $version
     *
     * @return void
     */
    public function setApiVersion($version = null)
    {
        if (!empty($version)) {
            $this->version = $version;
        }
    }

    /**
     * getApiVersion() function
     *
     * @return string version
     */
    public function getApiVersion()
    {
        return $this->version;
    }
}
