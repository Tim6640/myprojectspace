<?php
/**
 * Created by PhpStorm.
 * User: tbeek6640
 * Date: 15-11-2018
 * Time: 21:32
 */

class Model extends Crud implements IModel
{
    private $file_name;

    private $db_table;

    public function __construct()
    {
        parent::__construct($this->db_table);
        $this->file_name = static::class;
    }

    /**
     * @param string $db_table
     */
    public function setDbTable($db_table)
    {
        $this->db_table = $db_table;
    }

    public function createJsonFile($input)
    {
        $fileName = $this->file_name.'.json';
        $fp = fopen('data/'.$fileName, 'w');
        fwrite($fp, json_encode($input));
        fclose($fp);
    }

    public function __destruct()
    {
        unset($this->prop_db);
        unset($this->prop_sql);
        exit(0);
    }
}