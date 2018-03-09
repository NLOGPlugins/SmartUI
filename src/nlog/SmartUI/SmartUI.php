<?php

namespace nlog\SmartUI;

use nlog\SmartUI\util\UpdateManager;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use nlog\SmartUI\util\Settings;
use nlog\SmartUI\FormHandlers\FormManager;
use nlog\SmartUI\commands\OpenUICommand;

class SmartUI extends PluginBase implements Listener {


    const SETTING_VERSION = 1;

    /** @var SmartUI|null */
    private static $instance = null;

    /** @var string  */
    public static $prefix = "§b§l[SmartUI] §r§7";

    /**
     * @return SmartUI|null
     */
    public static function getInstance(): ?SmartUI {
        return static::$instance;
    }

    /** @var Settings|null */
    private $setting = null;

    /** @var FormManager|null */
    private $formManager = null;

    /** @var UpdateManager|null */
    private $updateManager = null;

    public function onLoad() {
        self::$instance = $this;
    }

    public function onEnable() {
        @mkdir($this->getDataFolder());
        $this->saveResource("settings.yml");
        $this->setting = new Settings($this->getDataFolder() . "settings.yml", $this);
        $this->formManager = new FormManager($this);
        $this->updateManager = new UpdateManager($this);
        $this->getServer()->getCommandMap()->register("smartui", new OpenUICommand($this));

        $this->updateManager->checkUpdate();
        $this->updateManager = null;
        $this->getLogger()->notice("SmartUI 플러그인이 활성화되었습니다.");
    }

    /**
     * @return Settings|null
     */
    public function getSettings(): ?Settings {
        return $this->setting;
    }

    /**
     * @return FormManager|null
     */
    public function getFormManager(): ?FormManager {
        return $this->formManager;
    }

}//클래스 괄호

?>