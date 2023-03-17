<?php

namespace Pandrome\Datagrid;

use Pandrome\Datagrid\DataGrid\Column\Builder as ColumnBuilder;
use Pandrome\Datagrid\DataGrid\DataRenderer;
use Pandrome\Datagrid\DataGrid\Filter\Builder as FilterBuilder;
use Pandrome\Datagrid\DataGrid\OrderBy;
use Pandrome\Datagrid\DataGrid\Page;
use Pandrome\Datagrid\DataGrid\QueryBuilder;

class DataGrid
{
    protected $model;
    protected $columns;
    protected $filters;
    protected $columnBuilder;
    protected $filterBuilder;
    protected $paginationPath = '';
    protected $allowedPerPage = '';
    protected $lockedFilters = [];
    protected $gridActions = [];

    public function __construct(string $model)
    {
        $this->model = $model;
    }

    public function model(): string
    {
        return $this->model;
    }

    public function columns(): array
    {
        return $this->columns;
    }

    public function filters(): array
    {
        return $this->filters;
    }

    public function setColumns(array $columns): DataGrid
    {
        $this->columns = $columns;

        return $this;
    }

    public function setfilters(array $filters = []): DataGrid
    {
        $this->filters = $filters;

        return $this;
    }

    public function setOrderBy(OrderBy $orderBy)
    {
        $this->orderBy = $orderBy;
    }

    public function setPage(Page $page)
    {
        $this->page = $page;
    }

    public function setPaginationPath(string $paginationPath)
    {
        $this->paginationPath = $paginationPath;
    }

    public function setAllowedPerPage(array $allowedPerPage)
    {
        $this->allowedPerPage = $allowedPerPage;
    }

    public function setLockedFilters(array $lockedFilters)
    {
        $this->lockedFilters = $lockedFilters;
    }

    public function setGridActions(array $gridActions)
    {
        $this->gridActions = $gridActions;
    }

    public function build(): array
    {
        $this->buildColumns();
        $this->buildFilters();
        return $this->buildPagination();
    }

    protected function buildPagination(): array
    {
        $pagination = (new QueryBuilder($this->model, $this->columnBuilder, $this->filterBuilder, $this->orderBy, $this->page, $this->paginationPath, $this->lockedFilters))->query();

        return (new DataRenderer($pagination, $this->columnBuilder, $this->filterBuilder, $this->orderBy, $this->allowedPerPage, $this->gridActions))->render();
    }

    protected function buildColumns()
    {
        $this->columnBuilder = new ColumnBuilder($this->columns, $this->model);
    }

    protected function buildFilters()
    {
        $this->filterBuilder = new FilterBuilder($this->filters);
    }
}