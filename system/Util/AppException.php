<?php

namespace Application\Util;

class AppException extends \Exception
{

    private $_options;

    public function __construct($message, 
                                $code = 1, 
                                $options = array(),
                                Exception $previous = null) 
    {
        parent::__construct($message, $code, $previous);

        $this->_options = $options; 
    }

    public function GetOptions() { return $this->_options; }
}