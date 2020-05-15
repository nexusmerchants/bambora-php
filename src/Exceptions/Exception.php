<?php

namespace NexusMerchants\Bambora\Exceptions;

/**
 * Exception class
 *
 * Beanstream specific exception types
 *
 * Zero error code corresponds to PHP API specific errors
 *
 * Positive error codes correspond to those of Beanstream API
 * @link http://developer.beanstream.com/documentation/take-payments/errors/
 * @link http://developer.beanstream.com/documentation/analyze-payments/errors/
 * @link http://developer.beanstream.com/documentation/analyze-payments/api-messages/
 * @link http://developer.beanstream.com/documentation/tokenize-payments/errors/
 *
 * Negative error codes corresponde to those of cURL
 * @link http://curl.haxx.se/libcurl/c/libcurl-errors.html
 *
 * @author Kevin Saliba
 */
class Exception extends \Exception
{
    /**
     * Exception: Message class variable
     *
     * @var string $message holds the human-readable error message string
     */
    protected $message;

    /**
     * Exception: Code class variable
     *
     * @var int $code holds the error message code (0=PHP, Positive=Beanstream API, Negative=cURL)
     */
    protected $code;

    /**
     * Constructor
     *
     * @param string $message Human-readable exception message
     * @param int $code Exception code (0=PHP[default], Positive=Beanstream API, Negative=cURL)
     */
    public function __construct($message, $code = 0)
    {
        //set class vars
        $this->message = $message;
        $this->code = $code;

        //send to super
        parent::__construct($this->message, $this->code);
    }
}
