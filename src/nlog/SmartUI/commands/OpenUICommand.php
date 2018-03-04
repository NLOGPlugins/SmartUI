<?php

namespace nlog\SmartUI\commands;

use pocketmine\command\PluginCommand;
use nlog\SmartUI\util\Translate;
use nlog\SmartUI\SmartUI;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\Player;

class OpenUICommand extends PluginCommand{
	
	public function __construct(SmartUI $owner) {
		parent::__construct("ui", $owner);
		$this->setLabel("ui");
		$this->setPermission(true);
		$this->setDescription("ui를 엽니다.");
	}
	
	public function execute(CommandSender $sender, string $commandLabel, array $args): bool {
		if (!$sender instanceof Player) {
			$sender->sendMessage(SmartUI::$prefix . "인게임에서 실행하세요.");
			return true;
		}
		$this->getPlugin()->getFormManager()->getMainMenuForm()->sendPacket($sender);
		return true;
	}
	
}
