<?php 

namespace Pandrome\Datagrid\DataGrid;

use Pandrome\Datagrid\DataGrid\Column\Builder as ColumnBuilder;
use Pandrome\Datagrid\DataGrid\Filter\Builder as FilterBuilder;
use Pandrome\Datagrid\DataGrid\Filter\TypeFilter;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Pandrome\Datagrid\DataGrid\OrderBy;
use Pandrome\Datagrid\DataGrid\Page;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class QueryBuilder
{
    protected $columnBuilder;
    protected $filterBuilder;
    protected $model;
    protected $orderBy;
    protected $page;
    protected $paginationPath;
    protected $lockedFilters;

    protected $with = [];
    
    
    public function __construct(string $model, ColumnBuilder $columnBuilder, FilterBuilder $filterBuilder, OrderBy $orderBy, Page $page, string $paginationPath, array $lockedFilters)
    {
        $this->columnBuilder = $columnBuilder;
        $this->filterBuilder = $filterBuilder;
        $this->model = $model;
        $this->orderBy = $orderBy;
        $this->page = $page;
        $this->paginationPath = $paginationPath;
        $this->lockedFilters = $lockedFilters;
    }

    public function query()
    {
        $model = new $this->model;
        $query = $model::orderByRaw($this->orderBy->forQuery());

        $this->handleColumns();
        $this->handleFilters($query);

        $this->handleRelations($query);
        
        $filters = $this->filterBuilder->filters();

        $pagination = $query->paginate($this->page->perPage(), ['*'], $this->page->key(), $this->page->page());
        $pagination->withPath($this->paginationPath);

        return $pagination->toArray();
    }

    protected function handleRelations(EloquentBuilder $query)
    {
        $query->with($this->with);
    }

    protected function handleFilters(EloquentBuilder $query)
    {
        foreach ($this->filterBuilder->filters() as $filter) {
            $this->handleFilter($filter, $query);
        }

        $this->handleLockedFilters($query);
    }

    protected function handleFilter(Filter $filter, EloquentBuilder $query)
    {
        $column = $this->columnBuilder->columnByName($filter->column());
        TypeFilter::filter($query, $column, $filter);
    }

    protected function handleLockedFilters(EloquentBuilder $query)
    {
        foreach($this->lockedFilters as $column => $options) {
            $this->addLockedFilter($query, $column, $options);
        }
    }

    protected function addLockedFilter(EloquentBuilder $query, string $column, array $options)
    {
        $operator = '=';
        if (isset($options['operator'])) {
            $operator = $options['operator'];
        } else if (!empty($options['value']) && is_array($options['value'])) {
            $operator = 'in';
        }
        
        $this->addLockedFilterByOperator($query, $operator, $column, $options['value'] ?? null);
    }

    protected function addLockedFilterByOperator(EloquentBuilder $query, string $operator, string $column, $value)
    {
        switch($operator) {
            case 'null':
                $query->whereNull($column);
                break;
            case 'notnull':
                $query->whereNotNull($column);
                break;
            default:
                if (is_null($value)) {
                    break;
                }
                $query->where($column, $operator, $value);
                break;
        }
    }

    protected function handleColumns()
    {
        foreach ($this->columnBuilder->columns() as $column) {
            $this->handleColumn($column);
        }
    }

    protected function handleColumn(Column $column)
    {
        if (!empty($column->relation)) {
            $this->with[] = $column->relation;
        }
    }
}