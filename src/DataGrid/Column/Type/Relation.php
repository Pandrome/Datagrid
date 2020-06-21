<?php

namespace Pandrome\Datagrid\DataGrid\Column\Type;

use Pandrome\Datagrid\DataGrid\Column;

class Relation implements IType
{
    public static function render(Column $column, array $data): array
    {
        return [
            'column' => $column->column,
            'type' => $column->type,
            'value' => static::values($column, $data)
        ];
    }

    protected static function values(Column $column, array $data)
    {
        $values = [];
        $columnParts = explode('.', $column->column);

        if (isset($data[$columnParts[0]])) {
            if (isset($columnParts[1])) {
                if (isset($data[$columnParts[0]][0])) {
                    foreach ($data[$columnParts[0]] as $row) {
                        if (isset($row[$columnParts[1]])) {
                            $values[] = $row[$columnParts[1]];
                        }
                    }
                } else if (isset($data[$columnParts[0]][$columnParts[1]])){
                    $values[] = $data[$columnParts[0]][$columnParts[1]];
                }
            }
            if (empty($values)) {
                $values = static::processOptions($column, $data[$columnParts[0]]);
            }
        }

        if (is_array($values)) {
            $values = !empty($values) ? implode(', ', $values) : $column->default;
        }

        return $values;
    }

    protected static function processOptions(Column $column, array $relationItems)
    {
        if (is_array($column->options)) {
            foreach ($column->options as $func => $parameters) {
                if (method_exists(__CLASS__, $func)) {
                    $relationItems = static::{$func}($relationItems, $parameters);
                }
            }
        }

        return $relationItems;
    }

    protected static function count(): int
    {
        return count(func_get_arg(0));
    }
}
