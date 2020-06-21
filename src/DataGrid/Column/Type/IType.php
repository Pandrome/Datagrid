<?php

namespace Pandrome\Datagrid\DataGrid\Column\Type;

use Pandrome\Datagrid\DataGrid\Column;

interface IType
{
    public static function render(Column $column, array $data): array;
}
