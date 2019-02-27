<?php

/**
 * Copyright (C) 2017-2019   NLOG (엔로그)

 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

namespace nlog\SmartUI;

use nlog\SmartUI\util\Settings;
use nlog\SmartUI\util\UpdateManager;
use pocketmine\plugin\PluginBase;
use nlog\SmartUI\FormHandlers\FormManager;
use nlog\SmartUI\commands\OpenUICommand;

class SmartUI extends PluginBase{


    const SETTING_VERSION = 1;

    /** @var SmartUI|null */
    private static $instance = null;

    /** @var string */
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

    public function onLoad(): void {
        static::$instance = $this;
    }

    public function onEnable(): void {
        @mkdir($this->getDataFolder());
        $this->saveResource("settings.yml");
        $this->setting = new Settings($this->getDataFolder() . "settings.yml", $this);
        $this->formManager = new FormManager($this);
        (new UpdateManager($this))->checkUpdate();

        $this->getServer()->getCommandMap()->register("smartui", new OpenUICommand($this));
        $this->getServer()->getLogger()->info("§bSmartUI §ehas been enabled.");
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
