<?php

namespace Library\BladeLikeEngine;

use BladeOneCustom;
use eftec\bladeone\BladeOne;
use framework\Http\View;

class BladeLikeView extends View
{
    public function render($isFullPath = false): string
    {
        $file = $isFullPath ? VIEW_FILE_ROOT . $this->file : $this->file;
        try {
            $parser = static::parser();
            $result = $parser->run($file, $this->data);
        } catch (\Exception $e) {
            ob_end_clean();
            throw $e;
        }
        return $result;
    }

    public static function parser(): BladeOneCustom
    {
        return new BladeOneCustom(
            VIEW_FILE_ROOT,
            null,
            BladeOneCustom::MODE_DEBUG
        );
    }
}
