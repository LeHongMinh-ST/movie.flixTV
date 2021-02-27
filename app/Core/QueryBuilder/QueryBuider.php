<?php


namespace App\Core\QueryBuilder;

use App\Core\Connection;

class QueryBuider
{
    private $columns = ['*'];

    protected $table;

    private $distinct = false;

    private $joins;

    private $wheres;

    private $orders;

    private $havings;

    private $limits;

    private $groups;

    private $offsets;

    protected $fillable = [];

    public function __construct()
    {
        $arguments = func_get_args();
        $numberOfArguments = func_num_args();

        if (method_exists($this, $function = '__construct' . $numberOfArguments)) {
            call_user_func_array(array($this, $function), $arguments);
        }
    }

    public function __construct1()
    {

    }

    public function __construct2($table)
    {
        $this->table = $table;
    }


    public static function table($table)
    {
        return new self($table);
    }

    public function select($column = ['*'])
    {
        $this->columns = is_array($column) ? $column : func_get_args();
        return $this;
    }

    public function distinct()
    {
        $this->distinct = true;
        return $this;
    }

    public function join($table, $first, $operator, $second)
    {
        $this->joins[] = [
            'table' => $table,
            'first' => $first,
            'operator' => $operator,
            'second' => $second,
            'type' => 'inner'
        ];

        return $this;
    }

    public function joinLeft($table, $first, $operator, $second)
    {
        $this->joins[] = [
            'table' => $table,
            'first' => $first,
            'operator' => $operator,
            'second' => $second,
            'type' => 'left'
        ];

        return $this;
    }

    public function joinRight($table, $first, $operator, $second)
    {
        $this->joins[] = [
            'table' => $table,
            'first' => $first,
            'operator' => $operator,
            'second' => $second,
            'type' => 'right'
        ];

        return $this;
    }


    public function where($condition, $operator = null, $value = null)
    {
        if (is_array($condition)) {
            foreach ($condition as $key => $value) {
                $this->wheres[] = [
                    'column' => $key,
                    'operator' => '=',
                    'value' => $value,
                    'type' => 'and'
                ];
            }

            return $this;
        }

        $this->wheres[] = [
            'column' => $condition,
            'operator' => $operator,
            'value' => $value,
            'type' => 'and'
        ];
        return $this;
    }

    public function orWhere($condition, $operator = null, $value = null)
    {
        if (is_array($condition)) {
            foreach ($condition as $key => $value) {
                $this->wheres[] = [
                    'column' => $key,
                    'operator' => '=',
                    'value' => $value,
                    'type' => 'or'
                ];
            }

            return $this;
        }

        $this->wheres[] = [
            'column' => $condition,
            'operator' => $operator,
            'value' => $value,
            'type' => 'or'
        ];
        return $this;
    }

    public function having($condition, $operator = null, $value = null)
    {
        if (is_array($condition)) {
            foreach ($condition as $key => $value) {
                $this->havings[] = [
                    'column' => $key,
                    'operator' => '=',
                    'value' => $value,
                    'type' => 'and'
                ];
            }

            return $this;
        }

        $this->havings[] = [
            'column' => $condition,
            'operator' => $operator,
            'value' => $value,
            'type' => 'and'
        ];
        return $this;
    }

    public function orHaving($condition, $operator = null, $value = null)
    {
        if (is_array($condition)) {
            foreach ($condition as $key => $value) {
                $this->havings[] = [
                    'column' => $key,
                    'operator' => '=',
                    'value' => $value,
                    'type' => 'or'
                ];
            }

            return $this;
        }

        $this->havings[] = [
            'column' => $condition,
            'operator' => $operator,
            'value' => $value,
            'type' => 'or'
        ];
        return $this;
    }

    public function orderBy($column, $directions = 'ASC')
    {
        $this->orders[] = [
            'column' => $column,
            'directions' => $directions
        ];
        return $this;
    }


    public function limit($limit)
    {
        $this->limits = $limit;
        return $this;
    }

    public function offset($offset)
    {
        $this->offsets = $offset;
        return $this;
    }

    public function groups($column)
    {
        $this->groups = is_array($column) ? $column : func_get_args();
        return $this;
    }

    public function insert($data)
    {

    }

    public function update($data, $where)
    {
        if (is_array($where)) {
            foreach ($where as $key => $value) {
                $this->wheres[] = [
                    'column' => $key,
                    'operator' => '=',
                    'value' => $value,
                    'type' => 'and'
                ];
            }

            return $this;
        }
    }

    public function delete($data, $where)
    {

    }

    public function first()
    {
        $data = $this->get();

        return reset($data);
    }

    public function get()
    {
        if (!isset($this->table) || empty($this->table)) return false;

        $query = $this->distinct ? "SELECT DISTINCT " : "SELECT ";

        if (isset($this->columns) && is_array($this->columns)) {
            $query .= implode(' ,', $this->columns);
        }

        $query .= ' FROM ' . $this->table;

        if (isset($this->joins) && is_array($this->joins)) {
            foreach ($this->joins as $join) {
                $table = $join['table'];
                $first = $join['first'];
                $operator = $join['operator'];
                $second = $join['second'];

                switch ($join['type']) {
                    case 'left':
                        $query .= ' LEFT JOIN ';
                        break;
                    case 'right':
                        $query .= ' RIGHT JOIN ';
                        break;
                    default:
                        $query .= ' INNER JOIN ';
                        break;
                }

                $query .= "$table ON $first $operator $second";
            }
        }

        if (isset($this->wheres) && is_array($this->wheres)) {
            $query .= " WHERE";
            foreach ($this->wheres as $key => $where) {
                $column = $where['column'];
                $operator = $where['operator'];
                $value = $where['value'];
                $type = $where['type'];

                $query .= " $column $operator $value";
                if ($key < count($this->wheres) - 1) {
                    $query .= ($type === 'and') ? " AND" : " OR";
                }
            }
        }

        if (isset($this->groups) && is_array($this->groups)) {
            $query .= " GROUP BY " . implode(' ,', $this->groups);
        }

        if (isset($this->havings) && is_array($this->havings)) {
            $query .= " HAVING";
            foreach ($this->havings as $key => $having) {
                $column = $having['column'];
                $operator = $having['operator'];
                $value = $having['value'];
                $type = $having['type'];

                $query .= " $column $operator $value";
                if ($key < count($this->havings) - 1) {
                    $query .= ($type === 'and') ? " AND" : " OR";
                }
            }
        }

        if (isset($this->orders) && is_array($this->orders)) {
            $query .= " ORDER BY";


            foreach ($this->orders as $key => $order) {
                $column = $order['column'];
                $directions = $order['directions'];
                $query .= " $column $directions";

                if ($key < count($this->orders) - 1) {
                    $query .= " ,";
                }
            }
        }

        if (isset($this->limits)) {
            $query .= " LIMIT " . $this->limits;
        }

        if (isset($this->offsets)) {
            $query .= " OFFSET " . $this->offsets;
        }

        $conn = new Connection();
        $result = $conn->getData($query);
        $data = [];

        while ($obj = $result->fetch()) {
            $data [] = $obj;
        }

        return $data;
    }


}