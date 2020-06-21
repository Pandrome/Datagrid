<?php

namespace Pandrome\Datagrid\DataGrid;

class OrderBy
{
    const DIRECTION_ASC = 'asc';
    const DIRECTION_DESC = 'desc';

    protected $direction;
    protected $directionOptions;
    protected $column;

    public function __construct(string $column, string $direction = null)
    {
        $this->column = $column;
        $this->direction = $direction;

        $this->directionOptions = [static::DIRECTION_ASC, static::DIRECTION_DESC];

        $this->processDirection();
    }

    public function forQuery(): string
    {
        return $this->column . $this->processDirection();
    }

    public function column(): string
    {
        return $this->column;
    }

    public function direction(): string
    {
        return $this->direction;
    }

    protected function processDirection(): string
    {
        if (stripos($this->column, $this->directionOptions[0]) !== false) {
            $this->column = trim(substr(0, stripos($this->column, $this->directionOptions[0])));
            $this->direction = $this->directionOptions[0];
            return '';
        }
        if (stripos($this->column, $this->directionOptions[1]) !== false) {
            $this->column = trim(substr(0, stripos($this->column, $this->directionOptions[1])));
            $this->direction = $this->directionOptions[1];
            return '';
        }
        
        if (in_array($this->direction, $this->directionOptions)) {
            return ' ' . $this->direction;
        }

        $this->direction = $this->directionOptions[0];

        return ' ' . $this->directionOptions[0];
    }
}