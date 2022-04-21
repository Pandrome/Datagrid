<?php

namespace Pandrome\Datagrid\DataGrid\Column\Type;

use Pandrome\Datagrid\DataGrid\Column;

class Text implements IType
{
    public static function render(Column $column, array $data): array
    {
        $text = $data[$column->column];
        if (isset($column->options['striptags']) && $column->options['striptags'] === true) {
            $text = strip_tags($text);
        }

        return [
            'column' => $column->column,
            'type' => $column->type,
            'value' => (string)$column->prefix . static::processOptions($column, (string)$text),
            'class' => static::addClass($column, (string)$text),
        ];
    }

    protected static function processOptions(Column $column, string $text): string
    {
        if (!empty($column->conditional_replace)) {
            if (isset($column->conditional_replace[$text])) {
                $text = $column->conditional_replace[$text]['value'];
            }
        }
        if (is_array($column->options)) {
            foreach ($column->options as $func => $parameters) {
                if (method_exists(__CLASS__, $func)) {
                    $text = static::{$func}($text, $parameters);
                }
            }
        }

        return $text;
    }

    protected static function round(string $text, $decimal = 2)
    {
        return number_format((float)$text, (int)$decimal, ',', '.');
    }

    protected static function addClass(Column $column, $value): string
    {
        $class = [];
        if (!empty($column->class)) {
            $class[] = $column->class;
        }
        if (!empty($column->conditional_replace)) {
            if (isset($column->conditional_replace[$value])) {
                if (!empty($column->conditional_replace[$value]['class'])) {
                    $class[] = $column->conditional_replace[$value]['class'];
                }
            }
        }

        return implode(' ', $class);
    }
}
