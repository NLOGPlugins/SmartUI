<?php

namespace nlog\SmartUI\FormHandlers;

use nlog\SmartUI\SmartUI;
use pocketmine\Player;

abstract class SmartUIForm {
	
	/** @var SmartUI */
	protected $owner;
	
	/** @var FormManager */
	protected $FormManager;
	
	/** @var int */
	public $formId;
	
	public function __construct(SmartUI $owner, FormManager $formManager, int $formId) {
		$this->owner = $owner;
		$this->FormManager = $formManager;
		$this->formId = $formId;
	}
	
	public final function getFormId(): int{
		return $this->formId;
	}
	
	abstract public static function getName(): string;
	
	abstract public static function getIdentifyName(): string;
	
	abstract protected function getFormData(Player $player);
	
	abstract public function sendPacket(Player $player);
	
	abstract public function handleRecieve(Player $player, $result);
	
}