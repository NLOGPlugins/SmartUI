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

namespace nlog\SmartUI\util;

use pocketmine\level\Level;
use pocketmine\utils\Config;
use pocketmine\Server;
use pocketmine\Player;
use onebone\economyapi\EconomyAPI;
use nlog\SmartUI\SmartUI;

class Settings {

    /** @var Config */
    protected $config;

    /** @var SmartUI */
    private $plugin;

    /** @var Server */
    protected $server;

    /** @var array */
    protected $availableParameter;

    public function __construct(string $path, SmartUI $plugin) {
        $this->plugin = $plugin;
        $this->config = new Config($path, Config::YAML);
        if ($this->config->get("version", 0) < SmartUI::SETTING_VERSION) {
            $plugin->saveResource("settings.yml", true); //TODO: 세팅 파일 업데이트 시 보존
        }
        $this->server = Server::getInstance();
        $this->availableParameter = [
                "@playername",
                "@playercount",
                "@playermaxcount",
                "@motd",
                "@mymoney",
                "@health",
                "@maxhealth",
                "@year",
                "@month",
                "@day",
                "@hour"
        ];
    }

    public function allowAutoUpdater() {
        return is_bool($this->config->get('auto-updater', true)) ? $this->config->get('auto-updater', true) : true;
    }

    public function rebootWhenServerRestart() {
        return is_bool($this->config->get('reboot-when-update', true)) ? $this->config->get('reboot-when-update', true) : true;
    }

    public function getItem() {
        return $this->config->get("item", "345:0");
    }

    public function getMessage(Player $player) {
        if (class_exists(EconomyAPI::class, true)) {
            $money = EconomyAPI::getInstance()->myMoney($player);
        } else {
            $money = "@mymoney";
        }
        $msg = $this->config->get("message");
        $msg = str_replace($this->availableParameter, [
                $player->getName(),
                count($this->server->getOnlinePlayers()),
                $this->server->getMaxPlayers(),
                $this->server->getNetwork()->getName(),
                $money,
                $player->getHealth(),
                $player->getMaxHealth(),
                date("Y"),
                date("m"),
                date("d"),
                date("g")
        ], $msg);

        $msg = str_replace('\n', "\n", $msg);

        return $msg;
    }

    public function canUseInWorld(Level $level): bool {
        $return = $this->config->getAll()["worlds"][strtolower($level->getFolderName())] ?? -1;
        if ($return < 0) {
            return true;
        }
        if (count($level->getPlayers()) >= $return) {
            return false;
        }
        return true;
    }

    public function canUse(string $functionIdentifyName): bool {
        $return = $this->config->getAll()["toggle"][$functionIdentifyName] ?? "on";
        $return = $return === "on" ? true : false;
        return $return;
    }

    public function getSetting(string $functionIdentifyName, string $key = "") {
        $function = $this->config->get($functionIdentifyName, null);
        if ($function === null) {
            return null;
        } elseif (!is_array($function) || trim($key) === "") {
            return $function;
        } else {
            return $function[$key] ?? null;
        }
    }
}