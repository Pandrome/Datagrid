<?php

namespace Pandrome\Datagrid\DataGrid\Grids;

use Illuminate\Http\Request;

interface IGrid
{
    public function __construct(array $parameters = null);
    public function build(): array;
}