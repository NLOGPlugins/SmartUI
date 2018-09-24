<?php
/**
 * Created by PhpStorm.
 * User: NLOG
 * Date: 2018-02-24
 * Time: 오전 8:31
 */

namespace nlog\SmartUI\util;

class Utils
{

	public static function koreanWonFormat(int $money): string {
		$elements = [];
		if($money >= 1000000000000){
			$elements[] = floor($money / 1000000000000) . "조";
			$money %= 1000000000000;
		}
		if($money >= 100000000){
			$elements[] = floor($money / 100000000) . "억";
			$money %= 100000000;
		}
		if($money >= 10000){
			$elements[] = floor($money / 10000) . "만";
			$money %= 10000;
		}
		if(count($elements) == 0 || $money > 0){
			$elements[] = $money;
		}
		return implode(" ", $elements) . "원";
	}

}
