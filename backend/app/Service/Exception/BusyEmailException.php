<?php

declare(strict_types=1);


namespace App\Service\Exception;


class BusyEmailException extends \RuntimeException
{
    public function __construct(string $email)
    {
        parent::__construct(sprintf("The email [%s] already in use", $email));
    }
}
