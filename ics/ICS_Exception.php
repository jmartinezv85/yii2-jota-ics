<?php 
/**
 * @Author: Jorge Martinez
 * @Date:   2017-02-07 23:45:45
 * @Last Modified by:   Jorge Martinez
 * @Last Modified time: 2017-02-08 00:35:34
 */

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