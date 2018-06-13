<?php
/**
 * Copyright (c) vdeApps 2018
 */

namespace App\Models;

use vdeApps\Import\ImportAbstract;
use XBase\Table;


class ImportDbf extends ImportAbstract {
    
    /** @var Table */
    protected $dbf = null;
    private $dbfCharset = null;
    private $dbfColumns = null;
    
    /**
     * @return $this|mixed
     * @throws \Exception
     */
    public function read() {
        
        $this->dbf = new Table($this->getFilename(), $this->getDbfColumns(), $this->getDbfCharset());
        
        /*
         * Add header columns
         */
        $headerCols = array_keys($this->dbf->getColumns());
        $this->addRow($headerCols);
        
        /*
         * Convert \DateTime to String
         */
        $this->addPlugins(function ($row) {
            foreach ($row as $item => &$value) {
                if (is_a($value, \DateTime::class)) {
                    /** @var \DateTime $value */
                    $value = $value->format('Y-m-d H:i:s.0');
                }
            }
            
            return $row;
        });
        
        while ($record = $this->dbf->nextRecord()) {
            $row = [];
            foreach ($headerCols as $key) {
                $row[] = $record->forceGetString($key);
            }
            $this->addRow($row);
        }
        $this->dbf->close();
        
        /** @var ImportInterfaceAbstract $this */
        return $this;
        
    }
    
    /**
     * @return null
     */
    public function getDbfColumns() {
        return $this->dbfColumns;
    }
    
    /**
     * @param null $columns
     *
     * @return ImportDbf
     */
    public function setDbfColumns($columns = null) {
        $this->dbfColumns = $columns;
        
        return $this;
    }
    
    /**
     * @return null
     */
    public function getDbfCharset() {
        return $this->dbfCharset;
    }
    
    /**
     * @param string|null $charset
     *
     * @return ImportDbf
     */
    public function setDbfCharset($charset = null) {
        $this->dbfCharset = $charset;
        
        return $this;
    }
    
    /**
     * @return Table
     */
    public function getDbf() {
        return $this->dbf;
    }
}