<?php

namespace Semok\Support\Theme\Exceptions;

use Semok\Support\Exceptions\RuntimeException;

class ThemeAlreadyExists extends RuntimeException
{

	public function __construct($theme)
	{
		parent::__construct("Theme {$theme->name} already exists", 1);
	}

}
