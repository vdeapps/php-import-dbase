<?php
/**
 * Copyright (c) vdeApps 2018
 */

namespace vdeApps\Import;

use org\majkel\dbase\Table;
use vdeApps\phpCore\Helper;

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
    
        /*
         * Convert \DateTime to String
         */
        $this->addPlugins(function ($row) {
            foreach ($row as $item => &$value) {
            
                if (is_a($value, \DateTime::class)) {
                    /** @var \DateTime $value */
                    $value = $value->format('Y-m-d H:i:s.0');
                }
                else{
                    if (!is_null($this->getDbfCharset())) {
                        
                        // charset from DBF to charset database
                        $value = iconv($this->getDbfCharset(), $this->getCharset(), $value);
                    }
                }
            }
        
            return $row;
        });
        
        $this->dbf = Table::fromFile($this->getFilename());
        
        $headerCols = $this->dbf->getFieldsNames();
        $this->addRow($headerCols);
    
        foreach ($this->dbf as $record) {
        
            $row = $record->toArray();
            $this->addRow($row);
        }
        
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