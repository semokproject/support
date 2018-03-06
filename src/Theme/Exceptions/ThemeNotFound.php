<?php

namespace Semok\Support\Theme\Exceptions;

use Semok\Support\Exceptions\RuntimeException;

class ThemeNotFound extends RuntimeException
{

	public function __construct($themeName)
	{
		parent::__construct("Theme $themeName not Found", 1);
	}

}
