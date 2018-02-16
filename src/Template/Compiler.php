<?php

namespace Semok\Support\Template;

use Blade;
use Exception;
use FatalThrowableError;
use Illuminate\View\Compilers\BladeCompiler;

class Compiler extends BladeCompiler {

    public function bladeCompile($value, array $args = array())
    {
      $php = Blade::compileString($value);

        $obLevel = ob_get_level();
        ob_start();
        extract($args, EXTR_SKIP);

        try {
            eval('?' . '>' . $php);
        } catch (Exception $e) {
            while (ob_get_level() > $obLevel) ob_end_clean();
            throw $e;
        } catch (Throwable $e) {
            while (ob_get_level() > $obLevel) ob_end_clean();
            throw new FatalThrowableError($e);
        }

        return ob_get_clean();
    }
}
