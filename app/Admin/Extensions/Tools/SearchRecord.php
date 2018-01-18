<?php

namespace App\Admin\Extensions\Tools;

use Encore\Admin\Admin;
use Encore\Admin\Grid\Tools\AbstractTool;

class SearchRecord extends AbstractTool
{
    /** @var array */
    private $records;

    public function __construct(array $records)
    {
        $this->records = $records;
    }

    protected function script()
    {

        return <<<EOT

EOT;
    }

    public function render()
    {
        Admin::script($this->script());

        return view('admin.tools.SearchRecord', ['records' => $this->records]);
    }
}