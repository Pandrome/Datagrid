<?php

namespace Pandrome\Datagrid\DataGrid\Filter\Type;

use Pandrome\Datagrid\DataGrid\Column;
use Pandrome\Datagrid\DataGrid\Filter;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

interface IType
{
    public static function buildQuery(EloquentBuilder $query, Column $column, Filter $filter);
}