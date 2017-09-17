<?php

require_once dirname(__FILE__) . '/../../../videos/configuration.php';
require_once dirname(__FILE__) . '/../../../objects/bootGrid.php';
require_once dirname(__FILE__) . '/../../../objects/user.php';

class LiveTransmition extends Object{
    
    protected $id, $title, $public, $saveTransmition, $users_id, $categories_id, $key, $description;

    protected static function getSearchFieldsNames() {
        return array('title');
    }

    protected static function getTableName() {
        return 'live_transmitions';
    }
    
    function getId() {
        return $this->id;
    }

    function getTitle() {
        return $this->title;
    }

    function getPublic() {
        return $this->public;
    }

    function getSaveTransmition() {
        return $this->saveTransmition;
    }

    function getUsers_id() {
        return $this->users_id;
    }

    function getCategories_id() {
        return $this->categories_id;
    }

    function getKey() {
        return $this->key;
    }

    function getDescription() {
        return $this->description;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setTitle($title) {
        global $global;
        $title = $global['mysqli']->real_escape_string($title);
        $this->title = $title;
    }

    function setPublic($public) {
        $this->public = $public;
    }

    function setSaveTransmition($saveTransmition) {
        $this->saveTransmition = $saveTransmition;
    }

    function setUsers_id($users_id) {
        $this->users_id = $users_id;
    }

    function setCategories_id($categories_id) {
        $this->categories_id = $categories_id;
    }

    function setKey($key) {
        $this->key = $key;
    }

    function setDescription($description) {
        global $global;
        $description = $global['mysqli']->real_escape_string($description);
        $this->description = $description;
    }

    function loadByUser($user_id) {
        $user = self::getFromDbByUser($user_id);
        if (empty($user))
            return false;
        foreach ($user as $key => $value) {
            $this->$key = $value;
        }
        return true;
    }
    
    static function getFromDbByUser($user_id) {
        global $global;
        $user_id = intval($user_id);
        $sql = "SELECT * FROM ".static::getTableName()." WHERE  users_id = $user_id LIMIT 1";
        $res = $global['mysqli']->query($sql);
        if ($res) {
            $user = $res->fetch_assoc();
        } else {
            $user = false;
        }
        return $user;
    }
    
    static function createTransmitionIfNeed($user_id) {
        $row = static::getFromDbByUser($user_id);
        if($row){
            return $row;
        }
        $l = new LiveTransmition(0);
        $l->setTitle("Empty Title");
        $l->setDescription("");
        $l->setKey(uniqid());
        $l->setCategories_id(1);
        $l->setUsers_id(User::getId());
        $l->save();
        return static::getFromDbByUser($user_id);
    }
    
    static function getFromDbByUserName($userName) {
        global $global;
        $userName = $global['mysqli']->real_escape_string($userName);
        $sql = "SELECT * FROM users WHERE user = '$userName' LIMIT 1";
        $res = $global['mysqli']->query($sql);
        if ($res) {
            $user = $res->fetch_assoc();
            return static::getFromDbByUser($user['id']);
        } else {
            return false;
        }
    }
    
    static function keyExists($key) {
        global $global;
        $sql = "SELECT * FROM ".static::getTableName()." WHERE  `key` = '$key' LIMIT 1";
        $res = $global['mysqli']->query($sql);
        if ($res) {
            $row = $res->fetch_assoc();
        } else {
            $row = false;
        }
        return $row;
    }


}