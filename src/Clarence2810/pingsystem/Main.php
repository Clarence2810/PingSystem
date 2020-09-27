<?php

namespace Clarence2810\pingsystem;

use pocketmine\{
	Player,
	Server,
	command\Command,
	command\CommandSender,
	plugin\PluginBase,
	utils\Textformat as C,
	utils\Config,
};;
class Main extends PluginBase
{
	public function onEnable(){
		$this->saveResource("config.yml");
		$this->saveDefaultConfig();
		$this->getScheduler()->scheduleRepeatingTask(new PingTask($this), 10);
		if($this->getConfig()->get("config-version") < 1 or $this->getConfig()->get("config-version") == null){
            $this->getLogger()->error("Your config file is outdated delete it and it will automatically updated!");
            $this->getServer()->getPluginManager()->disablePlugin($this);
        }
	}
	public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args):bool{
		if($cmd->getName() === "ping"){
			if(isset($args[0])){
				$player = $sender->getServer()->getPlayer($args[0]);
				if($player !== null){
					$this->pingMonitor($player, $sender);
				}else{
					$sender->sendMessage($this->getConfig()->get("player-not-found"));
				}
			}else{
				if($sender instanceof Player){
					$this->pingMonitor($sender, $sender);
				}else{
					$sender->sendMessage($this->getConfig()->get("player-only"));
				}
			}
		}
		return true;
	}
	public function pingMonitor($player, $sender){
		$ping = $player->getPing();
		if($ping <= $this->getConfig()->get("great-ping")){
			$sender->sendMessage(str_replace(["{ping}", "{player}"], [$this->getConfig()->get("great-ping-color") . $ping . $this->getConfig()->get("ping-symbol"), $player->getName()], $this->getConfig()->get("ping-message")));
		}else if($ping > $this->getConfig()->get("stable-ping-first") and $ping <= $this->getConfig()->get("stable-ping-second")){
			$sender->sendMessage(str_replace(["{ping}", "{player}"], [$this->getConfig()->get("stable-ping-color") . $ping . $this->getConfig()->get("ping-symbol"), $player->getName()], $this->getConfig()->get("ping-message")));
		}else if($ping > $this->getConfig()->get("bad-ping")){
			$sender->sendMessage(str_replace(["{ping}", "{player}"], [$this->getConfig()->get("bad-ping-color") . $ping . $this->getConfig()->get("ping-symbol"), $player->getName()], $this->getConfig()->get("ping-message")));
		}
	}
}