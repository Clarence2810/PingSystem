<?php

namespace Clarence2810\pingsystem;

use Clarence2810\pingsystem\Main;
use pocketmine\{
	Player,
	scheduler\Task,
};;
use InvalidArgumentException;
class PingTask extends Task
{
	private $plugin;
	
	public function __construct(Main $main){
		$this->main = $main;
	}
	public function onRun(int $currentTick){
		$symbol = $this->main->getConfig()->get("ping-symbol");
		foreach($this->main->getServer()->getOnlinePlayers() as $player){
			$ping = $player->getPing();
			if($this->main->getConfig()->get("ping-nametag") === true){
				if($ping <= $this->main->getConfig()->get("great-ping")){
					$player->setScoreTag($this->main->getConfig()->get("great-ping-color") . $ping . $symbol);
				}else if($ping > $this->main->getConfig()->get("stable-ping-first") and $ping <= $this->main->getConfig()->get("stable-ping-second")){
					$player->setScoreTag($this->main->getConfig()->get("stable-ping-color") . $ping . $symbol);
				}else if($ping > $this->main->getConfig()->get("bad-ping")){
					$player->setScoreTag($this->main->getConfig()->get("bad-ping-color") . $ping . $symbol);
				}
			}
			$color = $this->main->getConfig()->get("bad-ping-color");
			switch($this->main->getConfig()->get("bad-ping-system")){
				case "remind":
					if($ping > $this->main->getConfig()->get("bad-ping-alert")){
						$player->sendMessage(str_replace(["{ping}"], [$color . $ping . $symbol], $this->main->getConfig()->get("bad-ping-message-remind")));
					}
				break;
				case "kick":
					if($ping > $this->main->getConfig()->get("bad-ping-alert")){
						$player->kick(str_replace(["{ping}"], [$color . $ping . $symbol], $this->main->getConfig()->get("bad-ping-message-kick")));
					}
				break;
				default:
					throw new InvalidArgumentException("Invalid option at \plugin_data\PingSystem\resources\config.yml, line 53.");
			}
		}
	}
}