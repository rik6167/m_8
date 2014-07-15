<?php

/**
 * App_Errors.
 *
 */
class App_Errors
{
    // @var App_Errors $objInstance Singleton instance.
    protected static $_instance = null;

    // @var array $_errors Array of errors.
    protected $_errors = array();

    // @var string $_xhtml Html string.
    protected $_xhtml = null;

    /**
     * Constructor.
     *
     * @return void
     */
    private function __construct() {}
    
    /**
     * Singleton pattern implementation makes "clone" unavailable.
     *
     * @return void
     */
    private function __clone() {}     

    /**
     * Return an instance of Errors.
     *
     * @return object Errors object.
     */
    public static function getInstance()
    {
        if ( null === self::$_instance )
        {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /*
     * Add an error.
     *
     * @param string $message Error message.
     * @return void
     */
    public function add( $message )
    {
		$this->_errors[] = $message;
    }

    public function setErrors( Array $errors )
    {
        $this->_errors = $errors;

        return $this;
    }

    /**
     * Return errors.
     *
     * @return array Errors.
     */
    public function getErrors()
    {
		return $this->_errors;
    }

    /**
     * Draw view errors.
     *
     * @return string Errors output string.
     */
    public function draw()
    {
		// Check if the arrErrors structure is not empty.
		if ( false !== empty( $this->_errors ) )
		{
			return null;
		}
		// Add HTML ul tag to the return.
		$this->_xhtml .= "<ul>";

		// Loop arrErrors.
		foreach ( $this->_errors AS $message )
		{
			$this->_xhtml .= "<li>{$message}</li>";
		}

		$this->_xhtml .= "</ul>";

		// Return html.
		return $this->_xhtml;
    }

    public function __toString()
    {
        return $this->draw();
    }
}
