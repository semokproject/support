<?php

namespace Semok\Support\Theme\Exceptions;

use SemokTheme;
use Semok\Support\Exceptions\RuntimeException;

class ThemeException extends RuntimeException
{

	// Redefine the exception so message isn't optional
	public function __construct($message, $code = 0, Exception $previous = null)
	{

		$message .= ", Current Theme: [" . SemokTheme::get() . "]";

		// make sure everything is assigned properly
		parent::__construct($message, $code, $previous);
	}


}
