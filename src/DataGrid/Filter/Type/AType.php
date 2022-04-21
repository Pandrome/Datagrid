<?php

namespace Pandrome\Datagrid\DataGrid\Filter\Type;

use Pandrome\Datagrid\DataGrid\Column;
use Pandrome\Datagrid\DataGrid\Filter;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

class AType implements IType
{
    protected static $operator = '=';

    public static function buildQuery(EloquentBuilder $query, Column $column, Filter $filter)
    {
        if (isset($column->relation)) {
            static::useRelation($query, $column, $filter);

            return;
        }

        static::useDirect($query, $column, $filter);
    }

    protected static function useRelation(EloquentBuilder $query, Column $column, Filter $filter)
    {
        $columnName = $filter->column();
        $value = static::value($filter);

        $operator = static::$operator;
        $query->whereHas($column->relation, function ($query) use ($columnName, $operator, $value) {
            $columnParts = explode('.', $columnName);
            $column = array_pop($columnParts);
            return $query->where($column, $operator, $value);
        });
    }

    protected static function useDirect(EloquentBuilder $query, Column $column, Filter $filter)
    {
        $value = static::value($filter);
        if (!empty($column->scope_if_value)) {
            if ($value !== '') {
                $query = $query->{$column->scope_if_value}($value);
            }
            return;
        }
        $query = $query->where($filter->column(), static::$operator, $value);
    }

    protected static function value(Filter $filter)
    {
        return $filter->value();
    }
}
