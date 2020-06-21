<?php

namespace Pandrome\Datagrid\DataGrid\Column\Type;

use Carbon\Carbon;
use Pandrome\Datagrid\DataGrid\Column;

class DateTime implements IType
{
    public static function render(Column $column, array $data): array
    {
        return [
            'column' => $column->column,
            'type' => $column->type,
            'value' => !empty($data[$column->column]) ? Carbon::createFromTimestamp(strtotime($data[$column->column]))->format($column->format) : $column->default,
        ];
    }
}
