<?php

declare(strict_types=1);

namespace Medicode\Shared\Exceptions;

use Exception;

interface IAppException
{
}

class ApiException extends Exception implements IAppException
{
}

class ValidationException extends Exception implements IAppException
{
}