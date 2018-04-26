<?php

namespace nlog\SmartUI\FormHandlers;

use nlog\SmartUI\FormHandlers\forms\functions\CalculatorFunction;
use nlog\SmartUI\FormHandlers\forms\functions\CalendarFunction;
use nlog\SmartUI\FormHandlers\forms\functions\FlatMoveFunction;
use nlog\SmartUI\FormHandlers\forms\functions\IslandMoveFunction;
use nlog\SmartUI\FormHandlers\forms\functions\RecieveMoneyFunction;
use nlog\SmartUI\FormHandlers\forms\functions\ShowMoneyInfoFunction;
use nlog\SmartUI\FormHandlers\forms\functions\SpeakerFunction;
use nlog\SmartUI\FormHandlers\forms\functions\TellFunction;
use nlog\SmartUI\FormHandlers\forms\functions\WarpFunction;
use pocketmine\event\Listener;
use nlog\SmartUI\SmartUI;
use nlog\SmartUI\FormHandlers\forms\MainMenu;
use pocketmine\network\mcpe\protocol\ModalFormResponsePacket;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\Player;
use nlog\SmartUI\FormHandlers\forms\ListMenu;
use nlog\SmartUI\FormHandlers\forms\functions\SpawnFunction;
use nlog\SmartUI\FormHandlers\forms\functions\SendMoneyFunction;
use pocketmine\event\player\PlayerInteractEvent;

class FormManager implements Listener{
	
	/** @var SmartUI */
	private $owner;
	
	/** @var SmartUIForm[] */
	protected $functions;
	
	/** @var ListMenu */
	private $MainMenu;
	
	/** @var SmartUIForm */
	private $ListMenu;

    /**
     * FormManager constructor.
     *
     * @param SmartUI $owner
     */
	public function __construct(SmartUI $owner) {
		$this->owner = $owner;
		$owner->getServer()->getPluginManager()->registerEvents($this, $owner);
		
		$this->MainMenu = new MainMenu($owner, $this, 11918);
		$this->ListMenu = new ListMenu($owner, $this, 9182);
		
		$functions = [];
		//TODO: Implements FormID
		$functions[] = new SpawnFunction($owner, $this, 39388);
		$functions[] = new WarpFunction($owner, $this, 92838);
		$functions[] = new SpeakerFunction($owner, $this, 93821);
		$functions[] = new CalculatorFunction($owner, $this, 81721);
		$functions[] = new SendMoneyFunction($owner, $this, 38372);
		$functions[] = new RecieveMoneyFunction($owner, $this, 48392);
		$functions[] = new CalendarFunction($owner, $this, 91828);
        $functions[] = new IslandMoveFunction($owner, $this, 92810);
        $functions[] = new FlatMoveFunction($owner, $this, 90978);
        $functions[] = new ShowMoneyInfoFunction($owner, $this, 93102);
        $functions[] = new TellFunction($owner, $this, 63881);
		
		$this->functions = [];
		foreach ($functions as $function) {
			if ($this->owner->getSettings()->canUse($function->getIdentifyName())) {
				if ($function instanceof NeedPluginInterface && !$function->CompatibilityWithPlugin()) {
					continue;
				}
				$this->functions[$function->getFormId()] = $function;
			}
		}
	}
	
	/**
	 * 
	 * @param SmartUIForm $form
	 * @param bool $override
	 * @return bool
	 */
	public function addFunction(SmartUIForm $form, bool $override = false): bool {
		if (isset($this->functions[$form->getFormId()]) && !$override) {
			return false;
		}
		$this->functions[$form->getFormId()] = $form;
		return true;
	}
	
	/**
	 * 
	 * @param int $formId
	 * @return bool
	 */
	public function removeFunction(int $formId): bool {
		if (isset($this->functions[$formId])) {
			unset($this->functions[$formId]);
			return true;
		}
		return false;
	}
	
	/**
	 * 
	 * @return SmartUIForm[]
	 */
	public function getFunctions(): array{
		return $this->functions;
	}
	
	/**
	 * 
	 * @param int $formId
	 * @return SmartUIForm|NULL
	 */
	public function getFunction(int $formId): ?SmartUIForm{
		return $this->functions[$formId] ?? null;
	}
	
	/**
	 * 
	 * @return MainMenu
	 */
	public function getMainMenuForm() : MainMenu{
		return $this->MainMenu;
	}
	
	/**
	 *
	 * @return ListMenu
	 */
	public function getListMenuForm() : ListMenu{
		return $this->ListMenu;
	}
	
	public function onInteract(PlayerInteractEvent $ev) {
	    if (!$this->owner->getSettings()->canUseInWorld($ev->getPlayer()->getLevel())) {
	        $ev->getPlayer()->sendMessage(SmartUI::$prefix . "사용하실 수 없습니다.");
	        return;
        }
		if ($ev->getItem()->getId() . ":" . $ev->getItem()->getDamage() === $this->owner->getSettings()->getItem()) {
			$this->MainMenu->sendPacket($ev->getPlayer());
		}
	}
	
	public function onDataPacketRecieve(DataPacketReceiveEvent $ev) {
		$pk = $ev->getPacket();
		if ($pk instanceof ModalFormResponsePacket) {
			$player = $ev->getPlayer();
			if ($this->MainMenu->getFormId() === $pk->formId) {
				$this->MainMenu->handleRecieve($player, json_decode($pk->formData, true));
			}elseif ($this->ListMenu->getFormId() === $pk->formId) {
				$this->ListMenu->handleRecieve($player, json_decode($pk->formData, true));
			}elseif ($this->getFunction($pk->formId) instanceof SmartUIForm) {
				$this->getFunction($pk->formId)->handleRecieve($player, json_decode($pk->formData, true));
			}
		}
	}
	
}
