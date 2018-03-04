<?php

namespace nlog\SmartUI\FormHandlers;

use nlog\SmartUI\SmartUI;

interface NeedPluginInterface{
	
	public function CompatibilityWithPlugin(): bool;
	
}