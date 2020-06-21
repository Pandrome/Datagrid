<?php

namespace Pandrome\Datagrid\DataGrid;

use Pandrome\Datagrid\DataGrid\Column;

class Filter
{
    protected $column;
    protected $value;

    public function __construct($value, string $column)
    {
        $this->column = $column;
        $this->value = $value;
    }

    public function column(): string
    {
        return $this->column;
    }

    public function value()
    {
        return $this->value;
    }
}