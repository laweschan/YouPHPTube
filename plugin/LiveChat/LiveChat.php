<?php

require_once $global['systemRootPath'] . 'plugin/Plugin.abstract.php';
class LiveChat extends PluginAbstract{

    public function getDescription() {
        return "A live chat for multiple propouses";
    }
    
    public function getName() {
        return "LiveChat";
    }

    public function getUUID() {
        return "52222da2-3f14-49db-958e-15ccb1a07f0e";
    }
    
    public static function getChatPanelFile(){
        global $global;
        return $global['systemRootPath'].'plugin/LiveChat/view/panel.php';
    }
    
    public static function includeChatPanel($chatId = ""){
        global $global;
        if(Plugin::isEnabledByUUID(self::getUUID())){
            require static::getChatPanelFile();            
        }
    }

}