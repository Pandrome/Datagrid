<?php

namespace Pandrome\Datagrid\DataGrid\Filter\Type;

use Pandrome\Datagrid\DataGrid\Filter;

class Text extends AType
{
    protected static $operator = 'like';

    protected static function value(Filter $filter) {
        return '%' . $filter->value() . '%';
    }
}