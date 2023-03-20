<?php

namespace Pandrome\Datagrid\DataGrid\Column;

use Pandrome\Datagrid\DataGrid\Column;
use Pandrome\Datagrid\DataGrid\Filter\Builder as FilterBuilder;
use Pandrome\Datagrid\DataGrid\OrderBy;

class Builder
{
    protected $columns = [];
    protected $model;
    protected $columnsIndexed = [];

    public function __construct(array $columns, string $model)
    {
        $this->model = $model;
        $this->prepareColumns($columns);
    }

    public function columns(): array
    {
        return $this->columns;
    }

    public function columnByName(string $name): Column
    {
        return $this->columns[$this->columnsIndexed[$name]] ?? null;
    }

    protected function prepareColumns(array $columns)
    {
        foreach ($columns as $column) {
            $index = count($this->columns);
            $this->columns[] = new Column($column, $this->model);
            $this->columnsIndexed[$this->columns[$index]->column] = $index;
        }
    }

    public function renderData(array $pagination, FilterBuilder $filterBuilder, OrderBy $orderBy, array $allowedPerPage, array $gridActions): array
    {
        $pagination['headers'] = $this->renderHeaders($filterBuilder);
        $filters = $filterBuilder->filters();
        if (isset($filters[0]) && $filters[0]->column() == 'name') {
            //dump($pagination['data']);
        }
        $pagination['rows'] = $this->renderRows($pagination['data']);
        $pagination['sort'] = $orderBy->column();
        $pagination['direction'] = $orderBy->direction();
        $pagination['allowedPerPage'] = $allowedPerPage;
        $pagination['gridActions'] = $gridActions;

        return $pagination;
    }

    protected function renderRows(array $data): array
    {
        $rows = [];
        foreach ($data as $row) {
            $columns = [];
            foreach ($this->columns as $column) {
                $columns[] = $column->render($row);
            }
            $rows[] = $columns;
        }

        return $rows;
    }

    protected function renderHeaders(FilterBuilder $filterBuilder): array
    {
        $headers = [];
        foreach ($this->columns as $column) {
            $headers[] = $column->renderHeader($filterBuilder);
        }

        return $headers;
    }
}