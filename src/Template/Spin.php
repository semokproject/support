<?php

namespace Semok\Support\Template;

class Spin
{
    public $try = 0;
    public function render($text, $blocks=array())
    {
        $blocks = is_array($blocks) ? $blocks : array();
        $process = true;
        if ($text) {
            do {
                if ($this->try > 50) return false;
                $result = $this->process($text);
                if (!in_array($result,$blocks)) {
                    return $result;
                }
                $this->try++;
            }
            while($process);
        }
        return $text;
    }

    public function process($text)
    {
        if (!$text) return $text;

        return preg_replace_callback(
            '/\[(((?>[^\[\]]+)|(?R))*)\]/x',
            array($this, 'replace'),
            $text
        );
    }

    public function replace($text)
    {
        $text = $this->render($text[1]);
        $parts = explode('|', $text);
        return $parts[array_rand($parts)];
    }
}
