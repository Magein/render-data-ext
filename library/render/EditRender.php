<?php

namespace Magein\renderDataExt\library\render;

use Magein\renderData\library\render\form\TextareaRender;

class EditRender extends TextareaRender
{
    protected $class = 'edit-container';

    protected function render()
    {
        $value = $this->value;

        $this->value = null;

        $attr = $this->attr();

        $input = '<textarea id="edit-container" ' . $attr . '>' . $value . '</textarea>';

        return $input;
    }
}