<?php

namespace Tests\vdeApps\Import\ImportDbf;

use PHPUnit\Framework\TestCase;
use vdeApps\Import\ImportAbstract;
use vdeApps\Import\ImportDbf;

class ImportDbfTest extends TestCase{
    
    private $conn = null;
    
    public function createDb() {
        $user = 'vdeapps';
        $pass = 'vdeapps';
        $path = __DIR__.'/files/database.db';
        $memory = false;
        
        $config = new \Doctrine\DBAL\Configuration();
        try {
            $connectionParams = [
                'driver' => 'pdo_sqlite',
                'user'   => $user,
                'pass'   => $pass,
                'path'   => $path,
                'memory' => $memory,
            ];
            $this->conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);
        }
        catch (\Exception $ex) {
            $this->conn = false;
            throw new Exception("Failed to create connection", 10);
        }
        
        return $this->conn;
    }
    
    public function testImport1() {
    
        $this->createDb();
    
    
        $charsetDbf = 'cp863';
        $charsetDb = 'utf8';
    
        $localFilename = __DIR__ . '/files/test.DBF';
        $tablename = 'import_dbf';
        $imp = new ImportDbf($this->conn);
    
        $imp
            ->fromFile($localFilename)
            ->setDbfCharset($charsetDbf)
            ->setCharset($charsetDb)
            //                ->setLimit(10)
            // Destination table
            ->setTable($tablename)
            //Ignore la premiere ligne
            ->setIgnoreFirstLine(false)
            // Prend la première ligne comme entête de colonnes
            ->setHeaderLikeFirstLine(true)
            // Colonnes personnalisées
            //                            ->setFields($customFields)
            // Ajout de champs supplémentaires
                            ->addFields(['calc_iduser', 'calc_ident'])
            // Ajout de n colonnes
//            ->addFields(10)
            // Ajout d'un plugins
            ->addPlugins([$imp, 'pluginsNullValue'])
            // Ajout d'un plugins
            //                ->addPlugins(function ($rowData) {
            //                    $rowData['calcIduser'] = 'from plugins:' . $rowData['pkChantier'];
            //                    $rowData['calcIdent'] = 'from plugins:' . $rowData['uri'];
            //
            //                    return $rowData;
            //                })
            // required: Lecture/vérification
            ->read()
            // Exec import
            ->import();
    
        $this->assertEquals(1960, count($imp->getRows()));
    }
    
    
}