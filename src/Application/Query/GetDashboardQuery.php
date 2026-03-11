<?php
namespace App\Application\Query;

class GetDashboardQuery
{
    private int $page;
    private int $perPage;
    
    public function __construct(int $page, int $perPage = 50)
    {
        $this->page = $page;
        $this->perPage = $perPage;
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function getPerPage(): int
    {
        return $this->perPage;
    }
}
