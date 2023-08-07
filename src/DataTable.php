<?php

namespace Elbot\Majestic;

class DataTable {

    protected $tableName;
    protected $tableHeaders;
    protected $tableParams;
    protected $tableRows;

    # Constructs a new instance of the DataTable class.
    public function __construct() {
        $this->tableName = "";
        $this->tableHeaders = array();
        $this->tableParams = array();
        $this->tableRows = array();
    }

    # Set table's name
    public function setTableName($name) {
        $this->tableName = $name;
    }

    # Set table's headers
    public function setTableHeaders($headers) {
        $this->tableHeaders = $this->split($headers);
    }

    # Set table's parameters
    public function setTableParams($name, $value) {
        $this->tableParams[$name] = $value;
    }

    # Set table's rows
    public function setTableRow($row) {
        $rowsHash = array();
        $elements = $this->split($row);
        for ($i = 0; $i < count($elements); $i++) {
            if ($elements[$i] == " ") {
                $elements[$i] = "";
            }

            $rowsHash[$this->tableHeaders[$i]] = $elements[$i];
        }

        array_push($this->tableRows, $rowsHash);
    }

    # Splits the input from pipe separated form into an array.
    private function split($value) {
        $array = preg_split("/(?<!\|)\|(?!\|)/", $value, -1);

        for ($i = 0; $i < count($array); $i++) {
            $array[$i] = str_replace("||", "|", $array[$i]);
        }

        return $array;
    }

    # Returns the table's name
    public function getTableName() {
        return $this->tableName;
    }

    # Returns the table's headers
    public function getTableHeaders() {
        return $this->tableHeaders;
    }

    # Returns the table's parameters
    public function getParams() {
        return $this->tableParams;
    }

    # Returns a specific parameter from the table's parameters
    public function getParamForName($name) {
        if (!array_key_exists($name, $this->tableParams)) {
            return NULL;
        }

        return $this->tableParams[$name];
    }

    # Returns the number of rows in the table
    public function getRowCount() {
        return count($this->tableRows);
    }

    # Returns the table's rows
    public function getTableRows() {
        return $this->tableRows;
    }

}
