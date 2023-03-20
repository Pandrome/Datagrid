<?php

namespace Pandrome\Datagrid\DataGrid;

use Pandrome\Datagrid\DataGrid\Column\Builder as ColumnBuilder;
use Pandrome\Datagrid\DataGrid\Filter\Builder as FilterBuilder;


class DataRenderer
{
    protected $pagination;
    protected $columnBuilder;
    protected $filterBuilder;
    protected $orderBy;
    protected $allowedPerPage;
    protected $gridActions;

    public function __construct(array $pagination, ColumnBuilder $columnBuilder, FilterBuilder $filterBuilder, OrderBy $orderBy, array $allowedPerPage, array $gridActions)
    {
        $this->pagination = $pagination;
        $this->columnBuilder = $columnBuilder;
        $this->filterBuilder = $filterBuilder;
        $this->orderBy = $orderBy;
        $this->allowedPerPage = $allowedPerPage;
        $this->gridActions = $gridActions;
    }

    public function render(): array
    {
        return $this->columnBuilder->renderData($this->pagination, $this->filterBuilder, $this->orderBy, $this->allowedPerPage, $this->gridActions);
    }
}