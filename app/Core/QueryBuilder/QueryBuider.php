<?php


namespace App\Core\QueryBuilder;

use App\Core\Connection;
use stdClass;

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

    private $page;

    private $total;


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
        $this->total = count($this->get());
    }

    public function __construct2($table)
    {
        $this->table = $table;
    }

    /**
     *
     * Hàm lấy tên bảng để truy vấn
     *
     * @param string $table tên bảng dùng để truy vấn*
     * @return void
     *
     */
    public static function table($table)
    {
        return new self($table);
    }

    /**
     *
     * Hàm SELECT các cột cần lấy ra
     *
     * @param string $column xử lý các cột trong câu lệnh select
     * @return $this đối tượng truy vấn
     *
     */
    public function select($column = ['*'])
    {
        $this->columns = is_array($column) ? $column : func_get_args();
        return $this;
    }

    /**
     *
     * Hàm xác định thuộc tính DISTINCT
     *
     * @param null
     * @return $this đối tượng truy vấn
     *
     */
    public function distinct()
    {
        $this->distinct = true;
        return $this;
    }


    /**
     *
     * Hàm thiết lập các chỉ số của câu lệnh JOIN
     *
     * @param $table tên bảng cần liên kết
     * @param $first khóa liên kết bảng thứ nhất
     * @param $operator toán tử liên kết
     * @param $second khóa liên kết bảng thứ hai
     * @return $this đối tượng truy vấn
     *
     */
    public function join($table, $first, $operator, $second, $type = 'inner')
    {
        $this->joins[] = [
            'table' => $table,
            'first' => $first,
            'operator' => $operator,
            'second' => $second,
            'type' => $type
        ];

        return $this;
    }


    /**
     *
     * HHàm thiết lập các chỉ số của câu lệnh JOIN LEFT
     *
     * @param $table tên bảng cần liên kết
     * @param $first khóa liên kết bảng thứ nhất
     * @param $operator toán tử liên kết
     * @param $second khóa liên kết bảng thứ hai
     * @return $this đối tượng truy vấn
     *
     */
    public function joinLeft($table, $first, $operator, $second)
    {
        return $this->join($table, $first, $operator, $second, 'left');
    }

    /**
     *
     * Hàm thiết lập các chỉ số của câu lệnh JOIN Right
     *
     * @param $table tên bảng cần liên kết
     * @param $first khóa liên kết bảng thứ nhất
     * @param $operator toán tử liên kết
     * @param $second khóa liên kết bảng thứ hai
     * @return $this đối tượng truy vấn
     *
     */
    public function joinRight($table, $first, $operator, $second)
    {
        return $this->join($table, $first, $operator, $second, 'right');
    }


    /**
     *
     * Hàm thiết lập các chỉ số của câu lệnh WHERE
     *
     * @param string|array $condition điều kiện truyền vào hoặc từ khóa so sánh
     * @param $operator toán tử so sánh
     * @param $value điều kiện truyền vào
     * @return $this đối tượng truy vấn
     *
     */
    public function where($condition, $operator = null, $value = null, $type = 'and')
    {
        if (is_array($condition)) {
            foreach ($condition as $key => $value) {
                $this->wheres[] = [
                    'column' => $key,
                    'operator' => '=',
                    'value' => $value,
                    'type' => $type
                ];
            }

            return $this;
        }

        $this->wheres[] = [
            'column' => $condition,
            'operator' => $operator,
            'value' => $value,
            'type' => $type
        ];
        return $this;
    }


    /**
     *
     * Hàm thiết lập các chỉ số của câu lệnh WHERE OR
     *
     * @param string|array $condition điều kiện truyền vào hoặc từ khóa so sánh
     * @param $operator toán tử so sánh
     * @param $value điều kiện truyền vào
     * @return $this đối tượng truy vấn
     *
     */
    public function orWhere($condition, $operator = null, $value = null)
    {
        return $this->where($condition, $operator = null, $value = null, 'or');
    }

    /**
     *
     * Hàm thiết lập các chỉ số của câu lệnh WHERE IN
     *
     * @param string $condition điều kiện truyền
     * @param $arrayValue mảng các điều kiện
     * @return $this đối tượng truy vấn
     *
     */
    public function whereIn($condition, $arrayValue = [])
    {
        $this->wheres[] = [
            'column' => $condition,
            'operator' => 'IN',
            'value' => '(' . implode(',', $arrayValue) . ')',
            'type' => 'and'
        ];

        return $this;
    }

    /**
     *
     * Hàm thiết lập các chỉ số của câu lệnh WHERE NOT IN
     *
     * @param string $condition điều kiện truyền
     * @param $arrayValue mảng các điều kiện
     * @return $this đối tượng truy vấn
     *
     */
    public function whereNotIn($condition, $arrayValue = [])
    {
        $this->wheres[] = [
            'column' => $condition,
            'operator' => 'NOT IN',
            'value' => '(' . implode(',', $arrayValue) . ')',
            'type' => 'and'
        ];

        return $this;
    }

    /**
     *
     * Hàm thiết lập các chỉ số của câu lệnh WHERE NOT
     *
     * @param string|array $condition điều kiện truyền vào hoặc từ khóa so sánh
     * @param $operator toán tử so sánh
     * @param $value điều kiện truyền vào
     * @return $this đối tượng truy vấn
     *
     */
    public function whereNot($condition, $operator = null, $value = null)
    {
        if (is_array($condition)) {
            foreach ($condition as $key => $value) {
                $this->wheres[] = [
                    'column' => 'NOT ' . $key,
                    'operator' => '=',
                    'value' => $value,
                    'type' => 'and'
                ];
            }

            return $this;
        }

        $this->wheres[] = [
            'column' => 'NOT ' . $condition,
            'operator' => $operator,
            'value' => $value,
            'type' => 'and'
        ];
        return $this;
    }

    public function having($condition, $operator = null, $value = null, $type = 'and')
    {
        if (is_array($condition)) {
            foreach ($condition as $key => $value) {
                $this->havings[] = [
                    'column' => $key,
                    'operator' => '=',
                    'value' => $value,
                    'type' => $type
                ];
            }

            return $this;
        }

        $this->havings[] = [
            'column' => $condition,
            'operator' => $operator,
            'value' => $value,
            'type' => $type
        ];
        return $this;
    }

    public function orHaving($condition, $operator = null, $value = null)
    {
        return $this->having($condition, $operator = null, $value = null, 'or');
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

    public function create(array $data = [])
    {
        $query = "INSERT INTO " . $this->table . " ";
        $keys = "";
        $values = "";
        if (is_array($data)) {
            $i = 0;

            foreach ($data as $key => $value) {
                $keys .= $key;
                $values .= "'" . $value . "'";
                if (++$i != count($data)) {
                    $keys .= ',';
                    $keys .= ',';
                }
            }
        }
        $query .= "(" . $keys . ") VALUES (" . $values . ")";

        $conn = new Connection();
        return $conn->query($query);
    }

    public function update($data, $where = null)
    {
        if (!isset($this->table) || empty($this->table)) return false;

        $query = "UPDATE " . $this->table . " SET ";

        $string = "";
        $i = 0;


        foreach ($data as $column => $value) {
            $string .= $column . " = " . "'" . $value . "'";
            $i++;

            if ($i != count($data)) {
                $string .= " , ";
            }
        }

        $query .= $string;

        if (isset($where) && is_array($where)) {
            foreach ($where as $key => $value) {
                $this->wheres[] = [
                    'column' => $key,
                    'operator' => '=',
                    'value' => $value,
                    'type' => 'and'
                ];
            }
        }
        if (isset($where) && !is_array($where)) {
            $this->wheres[] = [
                'column' => 'id',
                'operator' => '=',
                'value' => $where,
                'type' => 'and'
            ];
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
        $conn = new Connection();
        return $conn->query($query);
    }

    public function delete($where)
    {
        if (!isset($this->table) || empty($this->table)) return false;

        $query = "DELETE FROM " . $this->table;

        if (isset($where) && is_array($where)) {
            foreach ($where as $key => $value) {
                $this->wheres[] = [
                    'column' => $key,
                    'operator' => '=',
                    'value' => $value,
                    'type' => 'and'
                ];
            }
        }
        if (isset($where) && !is_array($where)) {
            $this->wheres[] = [
                'column' => 'id',
                'operator' => '=',
                'value' => $where,
                'type' => 'and'
            ];
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


        $conn = new Connection();
        return $conn->query($query);
    }

    public function find($id)
    {
        if (!isset($this->table) || empty($this->table)) return false;

        $query = $this->distinct ? "SELECT DISTINCT " : "SELECT * FROM " . $this->table;

        $this->wheres[] = [
            'column' => 'id',
            'operator' => '=',
            'value' => $id,
            'type' => 'and'
        ];

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

        $conn = new Connection();
        $result = $conn->getData($query);

        return $result->fetch();
    }

    public function first()
    {
        $data = $this->get();

        return reset($data);
    }

    public function queryData()
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

        return $query;
    }

    public function get()
    {
        $conn = new Connection();
        $result = $conn->getData($this->queryData());
        $data = [];

        while ($obj = $result->fetch()) {
            $data [] = $obj;
        }

        return $data;
    }

    public function pagination($limit = 10, $page = 1)
    {
        $this->page = $page;

        $this->limit($limit);
        $this->offset((($this->page - 1) * $this->limits));
        $conn = new Connection();
        $result = $conn->getData($this->queryData());

        while ($row = $result->fetch()) {
            $results[] = $row;
        }


        $result = new stdClass();
        $result->page = $this->page;
        $result->limit = $this->limits;
        $result->total = $this->total;
        $result->data = $results;

        return $result;
    }

    public function createLinks( $links = 5, $list_class = '' ) {
        if ( $this->limits == '' ) {
            return '';
        }

        $last       = ceil( $this->total / $this->limits );

        $start      = ( ( $this->page - $links ) > 0 ) ? $this->page - $links : 1;
        $end        = ( ( $this->page + $links ) < $last ) ? $this->page + $links : $last;

        $html       = '<ul class="' . $list_class . '">';

        $class      = ( $this->page == 1 ) ? "disabled" : "";
        $html       .= '<li class="' . $class . '"><a href="?page=' . ( $this->page - 1 ) . '">&laquo;</a></li>';

        if ( $start > 1 ) {
            $html   .= '<li><a href="?&page=1">1</a></li>';
            $html   .= '<li class="disabled"><span>...</span></li>';
        }

        for ( $i = $start ; $i <= $end; $i++ ) {
            $class  = ( $this->page == $i ) ? "active" : "";
            $html   .= '<li class="' . $class . '"><a href="?&page=' . $i . '">' . $i . '</a></li>';
        }

        if ( $end < $last ) {
            $html   .= '<li class="disabled"><span>...</span></li>';
            $html   .= '<li><a href="?&page=' . $last . '">' . $last . '</a></li>';
        }

        $class      = ( $this->page == $last ) ? "disabled" : "";
        $html       .= '<li class="' . $class . '"><a href="?page=' . ( $this->page + 1 ) . '">&raquo;</a></li>';

        $html       .= '</ul>';

        return $html;
    }
}