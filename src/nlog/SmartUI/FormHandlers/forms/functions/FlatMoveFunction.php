<?php

namespace nlog\SmartUI\FormHandlers\forms\functions;

use ifteam\SimpleArea\database\area\AreaProvider;
use ifteam\SimpleArea\database\area\AreaSection;
use ifteam\SimpleArea\database\user\UserProperties;
use ifteam\SimpleArea\SimpleArea;
use nlog\SmartUI\FormHandlers\FormManager;
use nlog\SmartUI\FormHandlers\SmartUIForm;
use nlog\SmartUI\SmartUI;
use pocketmine\level\Position;
use pocketmine\Player;
use nlog\SmartUI\FormHandlers\NeedPluginInterface;
use pocketmine\network\mcpe\protocol\ModalFormRequestPacket;
use solo\swarp\SWarp;
use solo\swarp\Warp;

class FlatMoveFunction extends SmartUIForm implements NeedPluginInterface {

    const NOT_HAVE_FLAT = 0;

    /** @var array */
    protected $flatList;

    public function __construct(SmartUI $owner, FormManager $formManager, int $formId) {
        parent::__construct($owner, $formManager, $formId);
        $this->flatList = [];
    }

    public static function getName(): string {
        return "평야 이동";
    }

    public static function getIdentifyName(): string {
        return "moveflat";
    }

    public function CompatibilityWithPlugin(): bool {
        return class_exists(SimpleArea::class, true);
    }

    public function sendPacket(Player $player) {
        $pk = new ModalFormRequestPacket();
        $formData = $this->getFormData($player);
        if ($formData === self::NOT_HAVE_FLAT) {
            $player->sendMessage(SmartUI::$prefix . "소유한 평야가 없습니다.");
        }else{
            $pk->formData = $formData;
            $pk->formId = $this->formId;

            $player->dataPacket($pk);
        }
    }

    protected function getFormData(Player $player) {
        $json = [];
        $json['type'] = 'form';
        $json['title'] = "§6- 평야 이동";
        $json['content'] = "§b§l이동할 평야의 버튼을 눌러주세요.";
        $json["buttons"] = [];
        $ids = [];
        $flats = UserProperties::getInstance()->getUserProperties($player->getName(), "flat");
        if (empty($flats)) {
            return self::NOT_HAVE_FLAT;
        }
        foreach ($flats as $id => $xyz) {
            $ids[] = "§7> {$id}";
            $json['buttons'][] = ['text' => "§7▷ {$id}"]; //TODO: add image
        }
        $this->flatList[$player->getName()] = $ids;

        return json_encode($json);
    }

    public function handleRecieve(Player $player, $result) {
        if ($result === null) {
            unset($this->flatList[$player->getName()]);
            return;
        }
        if (!isset($this->flatList[$player->getName()])) {
            $this->owner->getLogger()->debug("비정상적인 응답입니다. {$player->getName()}, {$this->getName()}");
            return;
        }
        $id = $this->flatList[$player->getName()][$result];
        $areaSection = AreaProvider::getInstance()->getAreaToId("flat", $id);
        $level = $this->owner->getServer()->getLevelByName('flat');
        if (!$areaSection instanceof AreaSection) {
            $player->sendMessage(SmartUI::$prefix . "평야가 존재하지 않습니다.");
        }else{
            $x = $areaSection->get("startX");
            $z = $areaSection->get("startZ");
            $y = $level->getHighestBlockAt($x, $z) + 2;
            $player->teleport(new Position($x, $y, $z, $level));
            $player->sendMessage(SmartUI::$prefix . "{$id}번 땅으로 이동하였습니다.");
        }
        unset($this->flatList[$player->getName()]);
    }

}