<?php

namespace Pandrome\Datagrid\DataGrid\Filter;

use Pandrome\Datagrid\DataGrid\Filter;

class Builder
{
    protected $filters = [];
    protected $filtersIndexed = [];

    public function __construct(array $filters)
    {
        $this->prepareFilters($filters);
    }

    public function filters(): array
    {
        return $this->filters;
    }

    public function filterByName(string $name)
    {
        return $this->filters[$this->filtersIndexed[$name] ?? null] ?? null;
    }

    protected function prepareFilters(array $filters)
    {
        foreach ($filters as $filter) {
            $index = count($this->filters);
            $this->filters[] = new Filter($filter['value'], $filter['column']);
            $this->filtersIndexed[$this->filters[$index]->column()] = $index;
        }
    }
}