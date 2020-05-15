<?php

namespace NexusMerchants\Bambora;

use NexusMerchants\Bambora\Api\Payments;
use NexusMerchants\Bambora\Api\Profiles;
use NexusMerchants\Bambora\Api\Reporting;

/**
 * Gateway class - Main class to facilitate comms with Beanstream Gateway,
 *
 * @author Kevin Saliba
 */
class Gateway
{
    /**
     * Config object
     *
     * Holds mid, apikey, platform, api version
     *
     * @var    \NexusMerchants\Bambora\Configuration $config
     */
    protected $config;

    /**
     * API Objects
     *
     * Holds API objects with appropriate config
     *
     * @var    \NexusMerchants\Bambora\Api\Payments $paymentsAPI
     * @var    \NexusMerchants\Bambora\Api\Profiles $profilesAPI
     * @var    \NexusMerchants\Bambora\Api\Reporting $reportingAPI
     */
    protected $paymentsAPI;
    protected $profilesAPI;
    protected $reportingAPI;

    /**
     * Constructor
     *
     * @param string $merchantId Merchant ID
     * @param string $apiKey API Access Passcode
     * @param string $platform API Platform
     * @param string $version API Version
     *
     * @throws \NexusMerchants\Bambora\Exceptions\ConfigurationException
     */
    public function __construct($merchantId, $apiKey, $platform = 'api', $version = 'v1')
    {
        $this->config = new Configuration();
        $this->config->setMerchantId($merchantId);
        $this->config->setApiKey($apiKey);
        $this->config->setPlatform($platform);
        $this->config->setApiVersion($version);
    }

    /**
     * getConfig() function
     *
     * @return \NexusMerchants\Bambora\Configuration this gateway's set config
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * payments() function
     *
     * Public facing function to return the configured payment API
     * All comms with the Payments API will go through this function
     *
     * @return \NexusMerchants\Bambora\Api\Payments this gateway's payment api object
     */
    public function payments()
    {
        if (empty($this->paymentsAPI)) {
            $this->paymentsAPI = new Payments($this->config);
        }
        return $this->paymentsAPI;
    }

    /**
     * profiles() function.
     *
     * Public facing function to return the configured profiles API
     * All comms with the Profiles API will go through this function
     *
     * @return \NexusMerchants\Bambora\Api\Profiles this gateway's profiles api object
     */
    public function profiles()
    {
        if (empty($this->profilesAPI)) {
            $this->profilesAPI = new Profiles($this->config);
        }
        return $this->profilesAPI;
    }

    /**
     * reporting() function
     *
     * Public facing function to return the configured reporting API
     * All comms with the Reporting API will go through this function
     *
     * @return \NexusMerchants\Bambora\Api\Reporting this gateway's reporting api object
     */
    public function reporting()
    {
        if (empty($this->reportingAPI)) {
            $this->reportingAPI = new Reporting($this->config);
        }
        return $this->reportingAPI;
    }
}
