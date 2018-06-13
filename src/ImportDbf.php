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
    private $dbfCharsetFrom = null;
    private $dbfCharsetTo = 'UTF-8';
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
                        $value = iconv($this->getDbfCharset(), $this->dbfCharsetTo,  $value);
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
     * @return $this
     */
    public function setDbfColumns($columns = null) {
        $this->dbfColumns = $columns;
        
        return $this;
    }
    
    /**
     * @return null
     */
    public function getDbfCharset() {
        return $this->dbfCharsetFrom;
    }
    
    /**
     * For the conversion before insert into database
     * @param null   $from
     * @param string $to
     *
     * @return $this
     */
    public function setDbfCharset($from=null, $to='UTF-8') {
        $this->dbfCharsetFrom = $from;
        $this->dbfCharsetTo = $to;
        
        return $this;
    }
    
    /**
     * @return Table
     */
    public function getDbf() {
        return $this->dbf;
    }
}