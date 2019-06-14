<?php

namespace Beanstream;

/**
 * Enpoints class to build, format and return API endpoint urls based on incoming platform and version
 *
 * @author Kevin Saliba
 */
class Endpoints
{
    /**
     * Endpoints: Set BASE API Endpoint URL with inline {0} platform variable
     */
    const BASE_URL_BAMBORANA = 'https://{0}.na.bambora.com';
    const BASE_URL_BEANSTREAM = 'https://{0}.beanstream.com';  // Keeping beanstream endpoint for backwards compatibility when $platform = 'www'

    /**
     * Endpoint URL holders
     *
     * Holds each of the URLS for the API endpoints
     * platform and version are added in the constructor
     *
     * @var string  $basePaymentsURL
     * @var string  $getPaymentURL
     * @var string  $baseProfilesURL
     * @var string  $preAuthCompletionsURL
     * @var string  $returnsURL
     * @var string  $voidsURL
     * @var string  $profileURI
     * @var string  $cardsURI
     * @var string  $reportsURL
     * @var string  $continuationsURL
     * @var string  $tokenizationURL
     */
    protected $basePaymentsURL;
    protected $getPaymentURL;
    protected $baseProfilesURL;
    protected $preAuthCompletionsURL;
    protected $returnsURL;
    protected $unreferencedReturnsURL;
    protected $voidsURL;
    protected $profileURI;
    protected $cardsURI;
    protected $reportsURL;
    protected $continuationsURL;
    protected $tokenizationURL;

    /**
     * Endpoint: incoming API Platform
     *
     * @var string $platform
     */
    protected $platform;

    /**
     * Endpoint: incoming API Version
     *
     * @var string $version
     */
    protected $version;

    /**
     * Constructor
     *
     * @param string $platform API Platform
     * @param string $version API Version
     */
    public function __construct($platform, $version)
    {
        // Assign endpoints
        $baseUrl = ($platform == 'www' ? self::BASE_URL_BEANSTREAM . '/api' : self::BASE_URL_BAMBORANA);

        // Payments
        $this->basePaymentsURL = $baseUrl . '/{1}/payments';
        $this->preAuthCompletionsURL = $this->basePaymentsURL . '/{2}/completions';
        $this->returnsURL = $this->basePaymentsURL . '/{2}/returns';
        $this->unreferencedReturnsURL = $this->basePaymentsURL . '/0/returns';
        $this->voidsURL = $this->basePaymentsURL . '/{2}/void';
        $this->continuationsURL = $this->basePaymentsURL . '/{2}/continue';
        $this->tokenizationURL = ($platform == 'www' ? self::BASE_URL_BEANSTREAM . '/scripts/tokenization/tokens' : $baseUrl . '/scripts/tokenization/tokens');

        // Profiles
        $this->baseProfilesURL = $baseUrl . '/{1}/profiles';
        $this->profileURI = $this->baseProfilesURL . '/{2}';
        $this->cardsURI = $this->profileURI . '/cards';
        $this->cardURI = $this->cardsURI . '/{3}';

        // Reporting
        $this->reportsURL = $baseUrl . '/{1}/reports';
        $this->getPaymentURL = $this->basePaymentsURL . '/{2}';

        // Assign incoming platform and version
        $this->platform = $platform;
        $this->version = $version;
    }

    // Methods to build out and return endpoints
    // Payments

    /**
     * getBasePaymentsURL() function
     *
     * @return string   Endpoint URL
     */
    public function getBasePaymentsURL()
    {
        // Parse url and replace variables via messageformat
        // return msgfmt_format_message('en_US', $this->basePaymentsURL, [$this->platform, $this->version]);

        // Or use less-stringent str_replace instead of msgfmt above
        return str_replace(['{0}', '{1}'], [$this->platform, $this->version], $this->basePaymentsURL);
    }

    /**
     * getContinuationsURL() function
     *
     * @param string $merchant_data The IDEBIT_MERCHDATA value returned by the Interac response
     * @return string   Endpoint URL
     */
    public function getContinuationsURL($merchant_data)
    {
        return str_replace(['{0}', '{1}', '{2}'], [$this->platform, $this->version, $merchant_data], $this->continuationsURL);
    }

    /**
     * getPreAuthCompletionsURL() function
     *
     * @param string $tid Transaction Id
     * @return string Endpoint URL
     */
    public function getPreAuthCompletionsURL($tid)
    {
        // Parse url and replace variables via messageformat
        // return msgfmt_format_message('en_US', $this->preAuthCompletionsURL, [$this->platform, $this->version, $tid));

        // Or use less-stringent str_replace instead of messageformat above
        return str_replace(['{0}', '{1}', '{2}'], [$this->platform, $this->version, $tid], $this->preAuthCompletionsURL);
    }

    /**
     * getReturnsURL() function
     *
     * @param string $tid Transaction Id
     * @return string Endpoint URL
     */
    public function getReturnsURL($tid)
    {
        // Parse url and replace variables via messageformat
        // return msgfmt_format_message('en_US', $this->returnsURL, [$this->platform, $this->version, $tid));

        // Or use less-stringent str_replace instead of messageformat above
        return str_replace(['{0}', '{1}', '{2}'], [$this->platform, $this->version, $tid], $this->returnsURL);
    }

    /**
     * getUnreferencedReturnsURL() function
     *
     * @return string Endpoint URL
     */
    public function getUnreferencedReturnsURL()
    {
        // Parse url and replace variables via messageformat
        // return msgfmt_format_message('en_US', $this->unreferencedReturnsURL, [$this->platform, $this->version]);

        // Or use less-stringent str_replace instead of messageformat above
        return str_replace(['{0}', '{1}'], [$this->platform, $this->version], $this->unreferencedReturnsURL);
    }

    /**
     * getVoidsURL() function
     *
     * @param string $tid Transaction Id
     * @return string Endpoint URL
     */
    public function getVoidsURL($tid)
    {
        // Parse url and replace variables via messageformat
        // return msgfmt_format_message('en_US', $this->voidsURL, [$this->platform, $this->version, $tid));

        // Or use less-stringent str_replace instead of messageformat above
        return str_replace(['{0}', '{1}', '{2}'], [$this->platform, $this->version, $tid], $this->voidsURL);
    }

    /**
     * getTokenURL() function
     *
     * @return string Endpoint URL
     */
    public function getTokenURL()
    {
        // Parse url and replace variables via messageformat
        // return msgfmt_format_message('en_US', $this->tokenizationURL, [$this->platform));

        // Or use less-stringent str_replace instead of messageformat above
        return str_replace(['{0}'], [$this->platform], $this->tokenizationURL);
    }


    // Profiles

    /**
     * getProfilesURL() function
     *
     * @return string Endpoint URL
     */
    public function getProfilesURL()
    {
        // Parse url and replace variables via messageformat
        // return msgfmt_format_message('en_US', $this->baseProfilesURL, [$this->platform, $this->version]);

        // Or use less-stringent str_replace instead of messageformat above
        return str_replace(['{0}', '{1}'], [$this->platform, $this->version], $this->baseProfilesURL);
    }

    /**
     * getProfileURI() function
     *
     * @param string $pid Profile Id
     * @return string Endpoint URL
     */
    public function getProfileURI($pid)
    {
        // Parse url and replace variables via messageformat
        // return msgfmt_format_message('en_US', $this->profileURI, [$this->platform, $this->version, $pid));

        // Or use less-stringent str_replace instead of messageformat above
        return str_replace(['{0}', '{1}', '{2}'], [$this->platform, $this->version, $pid], $this->profileURI);
    }

    /**
     * getCardsURI() function
     *
     * @param string $pid Profile Id
     * @return string Endpoint URL
     */
    public function getCardsURI($pid)
    {
        // Parse url and replace variables via messageformat
        // return msgfmt_format_message('en_US', $this->cardsURI, [$this->platform, $this->version, $pid));

        // Or use less-stringent str_replace instead of messageformat above
        return str_replace(['{0}', '{1}', '{2}'], [$this->platform, $this->version, $pid], $this->cardsURI);
    }

    /**
     * getCardURI() function
     *
     * @param string $pid Profile Id
     * @param string $cid Card Id
     * @return string Endpoint URL
     */
    public function getCardURI($pid, $cid)
    {
        // Parse url and replace variables via messageformat
        // return msgfmt_format_message('en_US', $this->cardURI, [$this->platform, $this->version, $pid, $cid));

        // Or use less-stringent str_replace instead of messageformat above
        return str_replace(['{0}', '{1}', '{2}', '{3}'], [$this->platform, $this->version, $pid, $cid], $this->cardURI);
    }


    // Reporting

    /**
     * getReportingURL() function
     *
     * @return string Endpoint URL
     */
    public function getReportingURL()
    {
        // Parse url and replace variables via messageformat
        // return msgfmt_format_message('en_US', $this->reportsURL, [$this->platform, $this->version]);

        // Or use less-stringent str_replace instead of messageformat above
        return str_replace(['{0}', '{1}'], [$this->platform, $this->version], $this->reportsURL);
    }

    /**
     * getPaymentUrl() function
     *
     * @param string $tid Transaction Id
     * @return string Endpoint URL
     */
    public function getPaymentUrl($tid)
    {
        // Parse url and replace variables via messageformat
        // return msgfmt_format_message('en_US', $this->getPaymentURL, [$this->platform, $this->version, $tid));

        // Or use less-stringent str_replace instead of messageformat above
        return str_replace(['{0}', '{1}', '{2}'], [$this->platform, $this->version, $tid], $this->getPaymentURL);
    }
}
