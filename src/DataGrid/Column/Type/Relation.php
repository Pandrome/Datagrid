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
        $values = static::retrieveRelationValues($column, $data);

        if (is_array($values)) {
            $values = !empty($values) ? implode(', ', $values) : $column->default;
        }

        return $values;
    }

    protected static function retrieveRelationValues(Column $column, array $data)
    {
        $recItArray = Column::recursiveIteratorArray($data);

        $values = [];
        $columnParts = explode('.', $column->column);
        $lastColumn = array_pop($columnParts);
        $columnParts = implode('.', $columnParts);
        if (isset($recItArray[$columnParts][0])) {
            foreach ($recItArray[$columnParts] as $row) {
                if (isset($row[$lastColumn])) {
                    $values[] = static::processOptions($column, $row[$lastColumn]);
                }
            }
        } else if (isset($recItArray[$column->column])) {
            $values = static::processOptions($column, is_array($recItArray[$column->column]) ? $recItArray[$column->column] : [$recItArray[$column->column]]);
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
