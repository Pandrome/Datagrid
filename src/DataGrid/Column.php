<?php

namespace Pandrome\Datagrid\DataGrid;

use Pandrome\Datagrid\DataGrid\Column\Type;
use Pandrome\Datagrid\DataGrid\Filter\Builder as FilterBuilder;
use Carbon\Carbon;
use ReflectionObject;
use ReflectionProperty;

class Column
{
    public $class = '';
    public $column;
    public $default = '';
    public $format = '';
    public $hasFilter = true;
    public $hasSort = true;
    public $label;
    public $model = '';
    public $options = [];
    public $relation;
    public $type = TYPE::TYPE_TEXT;
    public $values = [];
    public $prefix = '';

    protected $columnOptions;

    public function __construct(array $columnOptions, string $model)
    {
        $this->columnOptions = $columnOptions;
        $this->model = $model;
        $this->fillProperties();
    }

    public function render(array $data): array
    {
        return $this->renderColumn($data);
    }

    protected function renderColumn(array $data): array
    {
        $type = !empty($this->relation) ? Type::TYPE_RELATION : $this->type;
        if (!class_exists(__NAMESPACE__ .'\\Column\\Type\\' . $type)) {
            throw new \Exception('DataGrid column type ' . $type . ' does not exist');
        }

        return call_user_func(__NAMESPACE__ .'\\Column\\Type\\' . $type . '::render' , $this, $data);
    }

    public function renderHeader(FilterBuilder $filterBuilder): array
    {
        $filter = $filterBuilder->filterByName($this->column);
        $rendered = [
            'column' => $this->column,
            'label' => $this->label,
            'type' => $this->type,
            'value' => $this->renderHeaderValue($filter),
            'hasSort' => $this->hasFilter,
            'hasFilter' => $this->hasFilter
        ];

        if ($this->type == Type::TYPE_SELECT) {
            if (!empty($this->options)) {
                $rendered['options'] = $this->options;
            }

            if (!empty($this->relation) && empty($rendered['options'])) {
                $rendered['options'] = [['label' => '', 'value' => '']];
                $columnParts = explode('.', $this->column);
                $allOptions = (new $this->model)->{$columnParts[0]}()->getModel()::all();
                foreach ($allOptions as $option) {
                    if (isset($option[$columnParts[1]])) {
                        $rendered['options'][] = [
                            'label' => ucfirst($option[$columnParts[1]]),
                            'value' => $option[$columnParts[1]]
                        ];
                    }
                }
            }
        }

        return $rendered;
    }

    protected function renderHeaderValue(Filter $filter = null)
    {
        if ($this->type == Type::TYPE_DATETIME) {
            return $filter && !empty($filter->value()) ? Carbon::createFromDate($filter->value())->format($this->format) : '';
        }

        return $filter ? $filter->value() : '';
    }

    protected function fillProperties()
    {
        $properties = $this->getProperties();
        foreach ($properties as $property) {
            if (isset($this->columnOptions[$property->name])) {
                $this->{$property->name} = $this->columnOptions[$property->name];
            }
        }
    }

    private function getProperties(): array
    {
        $reflection = new ReflectionObject($this);
        return $reflection->getProperties(ReflectionProperty::IS_PUBLIC);
    }
}
