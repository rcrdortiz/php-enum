<?php

namespace Rcrdortiz\Enum\Exception;

use Exception;

class InvalidEnumConstantDefaultValue extends Exception
{
    public function __construct(string $constant, string $enumClass)
    {
        $message = "$constant default value must be of type string in $enumClass.";
        parent::__construct($message);
    }

}