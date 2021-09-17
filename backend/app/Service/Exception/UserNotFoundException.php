<?php

declare(strict_types=1);


namespace App\Service\Exception;


class UserNotFoundException extends \RuntimeException
{
    public function __construct(int $userId)
    {
        parent::__construct(sprintf('User [%d] not found', $userId));
    }
}
