<?php

namespace nlog\SmartUI\commands;

use pocketmine\command\PluginCommand;
use nlog\SmartUI\SmartUI;
use pocketmine\command\CommandSender;
use pocketmine\Player;

class OpenUICommand extends PluginCommand{
	
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
        if (!$this->getPlugin()->getSettings()->canUseInWorld($sender->getLevel())) {
            $sender->sendMessage(SmartUI::$prefix . "사용하실 수 없습니다.");
            return true;
        }
		$this->getPlugin()->getFormManager()->getMainMenuForm()->sendPacket($sender);
		return true;
	}
	
}