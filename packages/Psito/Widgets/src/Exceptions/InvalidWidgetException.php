<?php
namespace Psito\Widgets\Exceptions;

use Exception;

class InvalidWidgetException extends Exception
{
	protected $message = 'Widget class must be an instance of Psito\Widget';
}
