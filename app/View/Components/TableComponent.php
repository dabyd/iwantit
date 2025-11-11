<?php

namespace App\View\Components;

use Illuminate\View\Component;

class TableComponent extends Component
{
    public $message;
    public $data;
    public $fields;
    public $actions;
    public $createUrl;
    public $createText;

    public function __construct($message = null, $data = [], $fields = [], $actions = [], $createUrl = '', $createText = 'Create New')
    {
        $this->message = $message;
        $this->data = $data;
        $this->fields = $fields;
        $this->actions = $actions;
        $this->createUrl = $createUrl;
        $this->createText = $createText;
    }

    public function render()
    {
        return view('components.table-component');
    }
}