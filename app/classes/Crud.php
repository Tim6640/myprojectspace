<?php

/**
 * Created by PhpStorm.
 * User: tim11
 * Date: 4-10-2018
 * Time: 10:00
 */
class Crud extends DbConfig
{
    protected $prop_table;
    //naar protected
    protected $prop_db;

    protected $prop_sql;

    /**
     * Crud constructor.
     * @param string $table
     * Sets the database parameters for the DbConfig
     * Makes the database connection
     */
    function __construct($table)
    {
        parent::setPropHost(HOST);
        parent::setPropDatabase(DATABASE);
        parent::setPropUsername(USER);
        parent::setPropPassword(PASSWORD);
        $this->prop_db = parent::__construct();
        $this->prop_table = $table;
    }

    /**
     * @param array $columns_values
     * Columns are the fields you want to edit
     * Values is the content for these fields
     * Creates a record in the database
     */
    public function create($columns_values)
    {
        if(is_array($columns_values))
        {
            $stmt = "INSERT INTO $this->prop_table ";

            $columns = "(";
            $values = "(";
            foreach ($columns_values as $column => $value)
            {
                $columns .= "$column,";
                $values .= ":".$column.",";
            }
            $columns = substr($columns, 0, -1).")";
            $values = substr($values, 0, -1).")";
            $stmt .= "$columns VALUES $values";

            $this->prop_sql = $this->prop_db->prepare($stmt);
            $this->bindValues($columns_values);
            $this->prop_sql->execute();
        }
    }

    /**
     * @param array $columns
     * @param array $where_is
     * @param string $orderBy
     * @return array
     * returns the requested records from the database
     */
    public function read($columns, $where_is = array(), $orderBy = "")
    {
        $stmt = "SELECT ";
        $options = "";
        foreach($columns as $column){
            $options .= $column.",";
        }
        $columns = substr($options, 0, -1);
        $stmt .= $columns." FROM ".$this->prop_table;
        if(!empty($where_is) && empty($orderBy))
        {
            $stmt .= $this->prepareWheres($where_is);
            $this->prop_sql = $this->prop_db->prepare($stmt);
            $this->bindValues($where_is);
        }
        elseif(!empty($orderBy)) {
            $stmt .= " ORDER BY $orderBy";
            $this->prop_sql = $this->prop_db->prepare($stmt);
        }
        else {
            $this->prop_sql = $this->prop_db->prepare($stmt);
        }
        $this->prop_sql->execute();
        $result = $this->prop_sql->fetchAll(PDO::FETCH_ASSOC);
//        var_dump($stmt);
//        var_dump($result);
        return $result;
    }

    /**
     * @param array $columns_values
     * @param array $where_is
     * Updates the row based on the where_is
     */ 
    public function update($columns_values, $where_is = array(""))
    {
        if(is_array($columns_values)) {
            $stmt = "UPDATE $this->prop_table SET ";
            $columns = "";
            foreach ($columns_values as $column => $value) {
                $columns .= "$column = :$column,";
            }
            $stmt .= substr($columns, 0, -1);
            if(!empty($where_is)) {
                $stmt .= $this->prepareWheres($where_is);
            }

            $this->prop_sql = $this->prop_db->prepare($stmt);
            $this->bindValues($columns_values);
            $this->bindValues($where_is);
            $this->prop_sql->execute();
        }
    }

/*    public function update($columns_values, $where_is)
    {
        $countArrayColumns = count($columns_values);
        $counterColumns = 1;
        if(is_array($columns_values)) {
            $stmt = "UPDATE $this->prop_table SET ";
//            var_dump($columns_values);
            foreach ($columns_values as $column => $value) {
                $stmt .= $columns = "$column = :$column";
                if($counterColumns<$countArrayColumns){
                    $stmt .= ',';
                }
                $counterColumns++;

            }
            $stmt .= $this->prepareWheres($where_is);

            var_dump($stmt);
            $sql = $this->prop_db->prepare($stmt);
            $this->bindValues($sql, $columns_values);
            $this->bindValues($sql, $where_is);
            $sql->execute();
        }
    }*/

    /**
     * @param array $where_is
     * Deletes the row based on the where_is
     */
    public function delete($where_is)
    {
        $stmt = "DELETE FROM $this->prop_table";
        $stmt .= $this->prepareWheres($where_is);
        $this->prop_sql = $this->prop_db->prepare($stmt);
        $this->bindValues($where_is);
        $this->prop_sql->execute();
    }

    public function countBirthdays($join = "INNER", $columns, $secondTable, $onColumn, $where_is, $orderBy, $limit, $offset)
    {
        $table = $this->prop_table;
        $stmt = "SELECT ";
        $options = "";
        foreach($columns as $column){
            $options .= $column.",";
        }
        $columns = substr($options, 0, -1);

        $stmt .= $columns." FROM ".$table;
        $stmt .= " $join JOIN $secondTable ON $table.$onColumn = $secondTable.$onColumn";
        if(!empty($where_is)){
            $stmt .= $where_is;
        }
        if(!empty($orderBy)){
            $stmt .= " ORDER BY ". $orderBy;
        }
        if(!empty($limit)){
            $stmt .= " LIMIT ". $limit;
        }
        if(!empty($offset)){
            $stmt .= " OFFSET ". $offset;
        }
//        var_dump($stmt);
        $this->prop_sql = $this->prop_db->prepare($stmt);
//        $this->joinBindValues($where_is);
        $this->prop_sql->execute();
        $result = $this->prop_sql->fetchAll(PDO::FETCH_ASSOC);
//        var_dump($result);
        return $result;
    }

    /**
     * @param $columns
     * @param $secondTable
     * @param $onColumn
     * @param array $where_is
     * @param string $orderBy
     * @return $result
     */
    public function innerJoin($columns, $secondTable, $onColumn, $where_is, $orderBy = "", $limit = "", $offset = "")
    {
        $result = $this->setupJoin("INNER", $columns, $secondTable, $onColumn, $where_is, $orderBy);
        return $result;
    }

    /**
     * @param $columns
     * @param $secondTable
     * @param $onColumn
     * @param array $where_is
     * @param string $orderBy
     * @return $result
     */
    public function leftJoin($columns, $secondTable, $onColumn, $where_is = array(), $orderBy = "")
    {
        $result = $this->setupJoin("LEFT", $columns, $secondTable, $onColumn, $where_is, $orderBy);
        return $result;
    }

    /**
     * @param $columns
     * @param $secondTable
     * @param $onColumn
     * @param array $where_is
     * @param string $orderBy
     * @return $result
     */
    public function rightJoin($columns, $secondTable, $onColumn, $where_is = array(), $orderBy = "")
    {
        $result = $this->setupJoin("RIGHT", $columns, $secondTable, $onColumn, $where_is, $orderBy);
        return $result;
    }

    /**
     * @param $columns
     * @param $secondTable
     * @param $onColumn
     * @param array $where_is
     * @param string $orderBy
     * @return $result
     */
    public function fullOuterJoin($columns, $secondTable, $onColumn, $where_is = array(), $orderBy = "")
    {
        $result = $this->setupJoin("FULL OUTER", $columns, $secondTable, $onColumn, $where_is, $orderBy);
        return $result;
    }

    /**
     * @param $join
     * @param $columns
     * @param $secondTable
     * @param $onColumn
     * @param array $where_is
     * @param string $orderBy
     * @return mixed
     */
    private function setupJoin($join, $columns, $secondTable, $onColumn, $where_is, $orderBy = "", $limit = "", $offset = "")
    {
        $table = $this->prop_table;
        $stmt = "SELECT ";
        $options = "";
        foreach($columns as $column){
            $options .= $column.",";
        }
        $columns = substr($options, 0, -1);

        $stmt .= $columns." FROM ".$table;
        $stmt .= " $join JOIN $secondTable ON $table.$onColumn = $secondTable.$onColumn";
        if(!empty($where_is)){
            $stmt .= $this->joinPrepareWheres($where_is);
        }
        if(!empty($orderBy)){
            $stmt .= " ORDER BY ". $orderBy;
        }
        if(!empty($limit)){
            $stmt .= " LIMIT ". $limit;
        }
        if(!empty($offset)){
            $stmt .= " OFFSET ". $offset;
        }
//        var_dump($stmt);
        $this->prop_sql = $this->prop_db->prepare($stmt);
        $this->joinBindValues($where_is);
        $this->prop_sql->execute();
        $result = $this->prop_sql->fetchAll(PDO::FETCH_ASSOC);
//        var_dump($result);
        return $result;
    }

    /**
     * @param $where_is
     * @return string
     */
    private function prepareWheres($where_is)
    {
        $result = " WHERE ";
        if(count($where_is) > 1) {
            $wheres = "";
            foreach ($where_is as $column => $is) {
                $wheres .= "$column = :$column AND ";
            }
            $result .= substr($wheres, 0, -5);
        }
        else {
            $column = key($where_is);
            $result .= "$column = :$column";
        }
        return $result;
    }

    private function joinPrepareWheres($where_is)
    {
        $result = " WHERE ";
        if(count($where_is) > 1) {
            $wheres = "";
            foreach ($where_is as $column => $is) {
                $columnSecond = str_replace("." , "", $column);
                $wheres .= "$column = :$columnSecond AND ";
            }
            $result .= substr($wheres, 0, -5);
        }
        else {
            $column = key($where_is);
            $columnSecond = str_replace("." , "", $column);
            $result .= "$column = :$columnSecond";
        }
        return $result;
    }

    /**
     * @param $sql
     * @param $array
     */
    private function bindValues($array)
    {
        foreach($array as $column => $value)
        {
//            var_dump($column, $value);
            $this->prop_sql->bindValue(":$column", "$value");
        }
    }

    private function joinBindValues($array)
    {
        foreach($array as $column => $value)
        {
            $column = str_replace("." , "", $column);
            $this->prop_sql->bindValue(":$column", "$value");
        }
    }
}