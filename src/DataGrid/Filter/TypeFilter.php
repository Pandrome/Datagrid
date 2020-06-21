<?php

namespace Pandrome\Datagrid\DataGrid\Filter;

use Pandrome\Datagrid\DataGrid\Column;
use Pandrome\Datagrid\DataGrid\Filter;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

class TypeFilter
{
    public static function filter(EloquentBuilder $query, Column $column, Filter $filter)
    {
        $column->type ;
        if (!class_exists(__NAMESPACE__ .'\\Type\\' . $column->type)) {
            throw new \Exception('DataGrid filter type ' . $column->type . ' does not exist');
        }

        call_user_func(__NAMESPACE__ .'\\Type\\' . $column->type . '::buildQuery' , $query, $column, $filter);

    }
}