<?php 

namespace jmartinez\yii\ics;

use Exception;

class ICS_Exception extends Exception
{

	   /**
     * 
     *
     * @param string    $message
     * @param int       $code
     * @param Exception $previous
     */
    public function __construct($message, $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}