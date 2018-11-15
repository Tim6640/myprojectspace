<?php
/**
 * Created by PhpStorm.
 * User: tim11
 * Date: 15-11-2018
 * Time: 15:07
 */

interface IModel
{
    public function __construct();
    public function setDbTable($db_table);
    public function createJsonFile($input);
    public function __destruct();
}