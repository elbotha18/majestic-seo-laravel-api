<?php

namespace Elbot\Majestic;

use XMLReader;

class Response
{
    protected $responseAttributes;
    protected $params;
    protected $tables;

    public function __construct($xml = null)
    {
        $this->responseAttributes = [];
        $this->params = [];
        $this->tables = [];

        if ($xml !== null) {
            $this->importData($xml);
        }
    }

    public function constructFailedResponse($code = null, $errorMessage = null)
    {
        $this->responseAttributes = [];
        $this->params = [];
        $this->tables = [];

        if ($code !== null && $errorMessage !== null) {
            $this->responseAttributes["Code"] = $code;
            $this->responseAttributes["ErrorMessage"] = $errorMessage;
            $this->responseAttributes["FullError"] = $errorMessage;
        }
    }

    private function importData($xml)
    {
        $reader = new XMLReader();
        $reader->XML($xml, "UTF-8");
        $dataTable = null;

        while ($reader->read()) {
            if ($reader->nodeType == XMLReader::ELEMENT) {
                switch ($reader->name) {
                    case "Result":
                        $this->responseAttributes["Code"] = $reader->getAttribute("Code");
                        $this->responseAttributes["ErrorMessage"] = $reader->getAttribute("ErrorMessage");
                        $this->responseAttributes["FullError"] = $reader->getAttribute("FullError");
                        break;

                    case "GlobalVars":
                        if ($reader->hasAttributes) {
                            while ($reader->moveToNextAttribute()) {
                                $this->params[$reader->name] = $reader->value;
                            }
                        }
                        break;

                    case "DataTable":
                        $dataTable = new DataTable();
                        $dataTable->setTableName($reader->getAttribute("Name"));
                        $dataTable->setTableHeaders($reader->getAttribute("Headers"));

                        while ($reader->moveToNextAttribute()) {
                            if ("Name" != $reader->name && "Headers" != $reader->name) {
                                $dataTable->setTableParams($reader->name, $reader->value);
                            }
                        }

                        $this->tables[$dataTable->getTableName()] = $dataTable;
                        break;

                    case "Row":
                        $row = $reader->readString();
                        $dataTable->setTableRow($row);
                        break;
                }
            }
        }
    }

    public function getResponseAttributes()
    {
        return $this->responseAttributes;
    }

    public function isOK()
    {
        if ("OK" == $this->responseAttributes["Code"] || "QueuedForProcessing" == $this->responseAttributes["Code"]) {
            return true;
        }

        return false;
    }

    public function getCode()
    {
        return $this->responseAttributes["Code"];
    }

    public function getErrorMessage()
    {
        return $this->responseAttributes["ErrorMessage"];
    }

    public function getFullError()
    {
        return $this->responseAttributes["FullError"];
    }

    public function getGlobalParams()
    {
        return $this->params;
    }

    public function getParamForName($name)
    {
        if (!array_key_exists($name, $this->params)) {
            return null;
        }

        return $this->params[$name];
    }

    public function getTables()
    {
        return $this->tables;
    }

    public function getTableForName($name)
    {
        if (!array_key_exists($name, $this->tables)) {
            return new DataTable();
        }

        return $this->tables[$name];
    }
}
