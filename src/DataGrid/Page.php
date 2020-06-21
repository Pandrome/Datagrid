<?php

namespace Pandrome\Datagrid\DataGrid;

class Page
{
    protected $perPage;
    protected $page;
    protected $key;

    public function __construct(int $page, int $perPage, string $key)
    {
        $this->perPage = $perPage;
        $this->page = $page;
        $this->key = $key;
    }

    public function page(): int
    {
        return $this->page;
    }

    public function perPage(): int
    {
        return $this->perPage;
    }

    public function key(): string
    {
        return $this->key;
    }
}