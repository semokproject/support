<?php

namespace Semok\Support;

use App;
use Semok\Support\Template\Compiler;
use Semok\Support\Template\Spin;

class Template
{
	protected $data;

	public function __construct($data=false)
	{
		if (is_array($data)) {
			$this->init = new Compiler(App::make('files'), storage_path('views'));
			$this->data = $data;
		}
	}

	public function render($template)
	{
		if (is_array($this->data)) {
			$template = $this->init->bladeCompile($template, $this->data);
			return $this->spin($template);
		}else{
			return '';
		}
	}

	public function spin($text)
	{
        $spintax = new Spin();
        $result = $spintax->render($text);
        return $result;
    }

}
