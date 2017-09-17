<?php

if (empty($global['systemRootPath'])) {
    $global['systemRootPath'] = "../";
}
require_once $global['systemRootPath'] . 'videos/configuration.php';
require_once $global['systemRootPath'] . 'objects/user.php';

class Plugin extends Object {

    protected $id, $status, $object_data, $name, $uuid, $dirName;

    protected static function getSearchFieldsNames() {
        return array('name');
    }

    protected static function getTableName() {
        return 'plugins';
    }

    function getId() {
        return $this->id;
    }

    function getStatus() {
        return $this->status;
    }

    function getObject_data() {
        return $this->object_data;
    }

    function getName() {
        return $this->name;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setStatus($status) {
        $this->status = $status;
    }

    function setObject_data($object_data) {
        $this->object_data = $object_data;
    }

    function setName($name) {
        $this->name = $name;
    }
    function getUuid() {
        return $this->uuid;
    }

    function getDirName() {
        return $this->dirName;
    }

    function setUuid($uuid) {
        $this->uuid = $uuid;
    }

    function setDirName($dirName) {
        $this->dirName = $dirName;
    }

        
    static function getPluginByName($name){
        global $global;
        $sql = "SELECT * FROM ".static::getTableName()." WHERE name = '$name' LIMIT 1";
        $res = $global['mysqli']->query($sql);
        if ($res) {
            $row = $res->fetch_assoc();
        } else {
            $row = false;
        }
        return $row;
    }
    
    static function getPluginByUUID($uuid){
        global $global;
        $sql = "SELECT * FROM ".static::getTableName()." WHERE uuid = '$uuid' LIMIT 1";
        $res = $global['mysqli']->query($sql);
        if ($res) {
            $row = $res->fetch_assoc();
        } else {
            $row = false;
        }
        return $row;
    }
    
    function loadFromUUID($uuid){
        $this->uuid = $uuid;
        $row = static::getPluginByUUID($uuid);
        if(!empty($row)){
            $this->load($row['id']);
        }
    }
    
    static function isEnabledByName($name){
        $row = static::getPluginByName($name);
        if($row){
            return $row['status']=='active';
        }
        return false;
    } 
    
    static function isEnabledByUUID($uuid){
        $row = static::getPluginByUUID($uuid);
        if($row){
            return $row['status']=='active';
        }
        return false;
    } 
    
    static function getAvailablePlugins() {
        global $global;
        $dir = $global['systemRootPath']."plugin";
        $result = array();
        $cdir = scandir($dir);
        foreach ($cdir as $key => $value) {
            if (!in_array($value, array(".", ".."))) {
                if (is_dir($dir . DIRECTORY_SEPARATOR . $value)) {
                    $p = YouPHPTubePlugin::loadPlugin($value);
                    $obj = new stdClass();
                    $obj->name = $p->getName();
                    $obj->dir = $value;
                    $obj->uuid = $p->getUUID();
                    $obj->description = $p->getDescription();
                    $obj->installedPlugin = static::getPluginByUUID($obj->uuid);
                    $obj->enabled = (!empty($obj->installedPlugin['status']) && $obj->installedPlugin['status']==="active")?true:false;
                    $obj->id = (!empty($obj->installedPlugin['id']))?$obj->installedPlugin['id']:0;
                    $obj->data_object = $p->getDataObject();
                    $obj->databaseScript = !empty(static::getDatabaseFile($value));
                    $result[] = $obj;
                }
            }
        }
        return $result;
    }
    
    static function getDatabaseFile($pluginName){
        $filename = static::getDatabaseFileName($pluginName);
        if(!$filename){
            return false;
        }
        return file_get_contents($filename);
        
    }
    
    static function getDatabaseFileName($pluginName){
        global $global;
        $dir = $global['systemRootPath']."plugin";
        $filename = $dir . DIRECTORY_SEPARATOR . $pluginName . DIRECTORY_SEPARATOR . "install" . DIRECTORY_SEPARATOR . "install.sql";
        if(!file_exists($filename)){
            return false;
        }
        return $filename;
        
    }


    static function getAllEnabled() {
        global $global;
        $sql = "SELECT * FROM  ".static::getTableName()." WHERE status='active' ";

        $sql .= self::getSqlFromPost();
        
        $res = $global['mysqli']->query($sql);
        $rows = array();
        if ($res) {
            while ($row = $res->fetch_assoc()) {
                $rows[] = $row;
            }
        } else {
            //die($sql . '\nError : (' . $global['mysqli']->errno . ') ' . $global['mysqli']->error);
        }
        return $rows;
    }
    
}
