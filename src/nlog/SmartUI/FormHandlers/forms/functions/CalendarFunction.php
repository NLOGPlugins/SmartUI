<?php

namespace nlog\SmartUI\FormHandlers\forms\functions;

use nlog\SmartUI\FormHandlers\SmartUIForm;
use pocketmine\Player;
use pocketmine\network\mcpe\protocol\ModalFormRequestPacket;

class CalendarFunction extends SmartUIForm{
	
	public static function getName(): string{
		return "이번 달 달력";
	}
	
	public static function getIdentifyName(): string{
		return "calendar";
	}
	
	public function sendPacket(Player $player) {
		$pk = new ModalFormRequestPacket();
		$pk->formData = $this->getFormData($player);
		$pk->formId = $this->formId;
			
		$player->dataPacket($pk);
	}
	
	private function getCalendar() {
		//TODO: Cleaup source
		$output = "일  월  화  수  목  금  토";
		$output .= "\n§f";
		$s_Y = date("Y"); //연도 : year
		$s_m = date("m"); //달 : month
		
		$today = date("d");
		
		$s_n = date("N",mktime(0,0,0,$s_m,1,$s_Y)); //첫째날 요일
		
		# 1 => 월 ~ 7 => 일
		$s_t = date("t",mktime(0,0,0,$s_m,1,$s_Y)); //마지막날짜
		
		switch($s_n) {
			case 1:
				$output .= str_repeat("  ", 1);
				break;
			case 2:
				$output .= str_repeat("  ", 3);
				break;
			case 3:
				$output .= str_repeat("  ", 5);
				break;
			case 4:
				$output .= str_repeat("  ", 7);
				break;
			case 5:
				$output .= str_repeat("  ", 9);
				break;
			case 6:
				$output .= str_repeat("  ", 11);
				break;
		}
		
		$day = ++$s_n;
		
		for ($i = 1; $i <= $s_t; $i++) {
			if ($i < 10) {
				if ($i == date("d")) {
					$output .= " §a$i  §f";
				}elseif ($day === 7) {
					$output .= " §b$i  §f";
				}elseif ($day === 1){
					$output .= "§c$i  §f";
				}else{
					$output .= " $i  ";
				}
			}else{
				if ($i == date("d")) {
					$output .= "§a$i  §f";
				}elseif ($day === 7) {
					$output .= "§b$i  §f";
				}elseif ($day === 1){
					$output .= "§c$i  §f";
				}else{
					$output .= "$i  ";
				}
			}
			if (++$day === 8) {
				$output .= "\n";
				$day = 1;
			}
		}
		
		return $output;
	}
	
	protected function getFormData(Player $player) {
		$json = [];
		$json['type'] = 'modal';
		$json['title'] = "- 이번 달 달력";
		$json['content'] = $this->getCalendar();
		$json["button1"] = "≫ 메뉴로 돌아가기 ≪";
		$json["button2"] = "≫ 창 닫기 ≪";
		
		return json_encode($json);
	}
	
	public function handleRecieve(Player $player, $result) {
		if ($result) {
			$this->FormManager->getListMenuForm()->sendPacket($player);
		}
	}
	
}