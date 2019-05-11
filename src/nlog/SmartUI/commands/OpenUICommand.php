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

namespace nlog\SmartUI\commands;

use pocketmine\command\PluginCommand;
use nlog\SmartUI\SmartUI;
use pocketmine\command\CommandSender;
use pocketmine\Player;

class OpenUICommand extends PluginCommand {

    public function __construct(SmartUI $owner) {
        parent::__construct("ui", $owner);
        $this->setPermission(true);
        $this->setDescription("SmartUI를 오픈합니다.");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): bool {
        if (!$sender instanceof Player) {
            $sender->sendMessage(SmartUI::$prefix . "인게임에서 실행하세요.");
            return true;
        }
        if (!$this->getPlugin()->getSettings()->canUseInWorld($sender->getWorld())) {
            $sender->sendMessage(SmartUI::$prefix . "사용하실 수 없습니다.");
            return true;
        }
        $this->getPlugin()->getFormManager()->getMainMenuForm()->sendPacket($sender);
        return true;
    }

}