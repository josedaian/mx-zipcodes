<?php

namespace App\Exceptions;

use Exception;
use JetBrains\PhpStorm\Internal\LanguageLevelTypeAware;

class ZipCodeNotFound extends Exception
{
    public function __construct(string $zipCode)
    {
        parent::__construct(sprintf('The zipcode %s does not exist', $zipCode));
    }
}
