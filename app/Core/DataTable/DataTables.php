<?php


namespace App\Core\DataTable;


use App\Core\Model;
use App\Core\Request;

class DataTables
{
    private $datas = [];

    private $model;

    private $draw;

    private $recordsTotal;

    private $recordsFiltered;

    private $addColumns = [];

    private $editColumns = [];

    public function __construct($model)
    {
        $this->model = $model;
    }

    public static function of(Model $model)
    {
        return new self($model);
    }

    public function make()
    {
        $request = new Request();
        $request = $request->all();

        $this->draw = (int)$request['draw'];
        $this->recordsTotal = count($this->model->get());
        $start = $request['start'];
        $length = $request['length'];
        $search = $request['search']['value'];

        if ($search != "") {
            foreach ($request['columns'] as $key => $column) {
                if (!array_key_exists($column['data'],$this->addColumns)){
                    $this->model->orWhere($column['data'], 'like', '"%'.$search.'%"');
                }

            }
        }

        $datas = $this->model->offset($start)->limit($length)->get();

        foreach ($datas as $data) {
            foreach ($this->addColumns as $key => $column) {
                if (is_callable($column)){
                    $data->{$key} = call_user_func($column,$data);
                }else{
                    $data->{$key} = $column;
                }
            }

            foreach ($this->editColumns as $key => $column) {
                if (is_callable($column)){
                    $data->{$key} = call_user_func($column,$data);
                }else{
                    $data->{$key} = $column;
                }
            }
        }


        foreach ($datas as $data) {
            $this->datas[] = (array)$data;
        }

        return json([
            'draw' => $this->draw,
            'recordsTotal' => $this->recordsTotal,
            'recordsFiltered' => $this->recordsTotal,
            'data' => $this->datas,
        ]);
    }

    public function addColumn($name, $action)
    {
        $this->addColumns = [
            $name => $action
        ];
        return $this;
    }

    public function editColumn($name, $action)
    {
        $this->editColumns = [
            $name => $action
        ];
        return $this;
    }

}