<?php

/** ----[ReportBugs-V1.0]----
*
* Phát Hiện & Báo Cáo lỗi Lên OP, Bạn sẽ nhận được quà có giá trị từ [Money] đến 30k xu
*/

namespace BugsReward\ReportBugs;

use pocketmine\plugin\PluginBase;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\{Player, Server};
use pocketmine\utils\Config;
use pocketmine\event\player\{PlayerJoinEvent, PlayerQuitEvent};

class Main extends PluginBase implements Listener{
	public $tag = "§a[§c•§a]§aReportBugs§a[§c•§a]";
	
	public function onEnable(){
		$this->getServer()->getLogger()->info($this->tag . " §a§lEnable Plugin...");
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->sr = new Config($this->getDataFolder() . "Bugs.yml", Config::YAML);
	}
	
	public function onJoin(PlayerJoinEvent $ev){
		$player = $ev->getPlayer();
		if($this->sr->exists($player->getName())){
			if($player->isOp()){
				$player->sendMessage($this->tag . " §l§aTìm Thấy 1 Báo Cáo Lỗi, Vui Lòng Kiểm tra!");
			}
		}
	}
	
	public function onQuit(PlayerQuitEvent $ev){
		$player = $ev->getPlayer();
		$name = $player->getName();
		if($player->isOp()){
			$this->getServer()->broadcastMessage("§l§aAdmin/Operator§c ".$name."§a Đã Thoát Server!");
			foreach($this->sr->get($name) as $config){
				$player->sendMessage($config);
			}
		}
	}
	
	public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args): bool{
		switch($cmd->getName()){
			case "reportbug":
			if(!($sender instanceof Player)){
				$this->getServer()->getLogger()->warning("Please Use In Server!");
				return true;
			}
			$msg = "
§a----[Bugs Report]----
§c• §aExample:§d /reportbug [lỗi] [Nhận Xét/Chỉ Lỗi]
§c• Page §e[1/1]
§a---------------------
";
            
			if(isset($args[0]) || isset($args[1])){
				$sender->sendMessage($msg);
				return true;
			}
			foreach($this->getServer()->getOnlinePlayers() as $p){
				$this->sr->set($player->getName(), ["Lỗi" => $args[0], "Nhận Xét" => $args[1]]);
				$this->sr->save();
				$p->sendMessage($this->tag . "§a Báo Cáo Thành Công!");
				if($player->isOp()){
					$p->sendMessage($this->tag . "§b Có người Báo Cáo Lỗi Op Ơi!");
				}
			}
			return true;
		}
		return true;
	}
}