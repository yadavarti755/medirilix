<?php

namespace App\Services;

use App\Repositories\QueryRepository;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class QueryService
{
    protected $queryRepository;

    public function __construct()
    {
        $this->queryRepository = new QueryRepository();
    }

    public function findAllForDatatable()
    {
        return $this->queryRepository->findForDatatable();
    }

    public function delete($id)
    {
        return $this->queryRepository->delete($id);
    }
}
