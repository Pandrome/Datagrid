<?php

namespace Pandrome\Datagrid\DataGrid\Grids;

use Pandrome\Datagrid\DataGrid;
use Pandrome\Datagrid\DataGrid\OrderBy;
use Pandrome\Datagrid\DataGrid\Page;

abstract class AGrid implements IGrid
{
    const DS = DIRECTORY_SEPARATOR;
    protected $parameters;
    protected $dataGrid;
    protected $model = '';
    protected $columns = [];
    protected $filters = [];
    protected $allowedPerPage = [10, 50, 100, 200];
    protected $orderByKey = 'sort';
    protected $directionKey = 'direction';
    protected $perPageKey = 'perPage';
    protected $pageKey = 'page';
    protected $defaultSortDirection;
    protected $defaultSortColumn;
    protected $orderBy;
    protected $defaultPage = 1;
    protected $page;
    protected $paginationPath = '';
    protected $lockedFilters = [];

    public function __construct(array $parameters = null)
    {
        $this->parameters = (array)$parameters;
        $this->dataGrid = new DataGrid($this->model);
    }

    public function build(): array
    {
        $this->prepareColumns();
        $this->prepareFilters();
        $this->prepareOrderBy();
        $this->preparePage();

        $this->dataGrid->setColumns($this->columns);
        $this->dataGrid->setFilters($this->filters);
        $this->dataGrid->setOrderBy($this->orderBy);
        $this->dataGrid->setPage($this->page);
        $this->dataGrid->setPaginationPath($this->paginationPath);
        $this->dataGrid->setAllowedPerPage($this->allowedPerPage);
        $this->dataGrid->setLockedFilters($this->lockedFilters);
        
        return $this->dataGrid->build();
    }

    protected function prepareColumns() {}
    
    protected function prepareFilters()
    {
        $headersIndexedByColumn = [];
        if (!empty($this->parameters['headers'])) {
            foreach ($this->parameters['headers'] as $header) {
                $headersIndexedByColumn[$header['column']] = $header['value'];
            }

            foreach($this->columns as $column) {
                if (isset($headersIndexedByColumn[$column['column']])) {
                    $this->filters[] = [
                        'column' => $column['column'],
                        'value' => $headersIndexedByColumn[$column['column']]
                    ];
                }
            }
        }
    }

    protected function prepareOrderBy()
    {
        $defaultColumn = (new $this->model)->getKeyName();
        
        if (!empty($this->defaultSortColumn)) {
            $defaultColumn = $this->defaultSortColumn;
        }

        $this->orderBy = new OrderBy($this->parameters[$this->orderByKey] ?? $defaultColumn, $this->parameters[$this->directionKey] ?? $this->defaultSortDirection);
    }

    protected function prepareAmount()
    {
        if (!isset($this->parameters[$this->perPageKey]) || !in_array($this->parameters[$this->perPageKey], $this->allowedPerPage)) {
            $this->parameters[$this->perPageKey] = $this->allowedPerPage[0];
        }
    }

    protected function preparePage()
    {
        $this->prepareAmount();

        $this->page = new Page($this->parameters[$this->pageKey] ?? $this->defaultPage, $this->parameters[$this->perPageKey], $this->perPageKey);
    }
}