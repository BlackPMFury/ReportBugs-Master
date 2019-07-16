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
			//$player->sendMessage($this->sr->get($name));
		}
	}
	
	public function onCommand(CommandSender $sender, Command $cmd, string $label, array $ar): bool{
		switch(strtolower($cmd->getName())){
			case "reportbug":
			if(!($sender instanceof Player)){
				$this->getServer()->getLogger()->warning("Please Use In Server!");
				return true;
			}
			$msg = "
§a----[Bugs Report]----
§c• §aExample:§d /reportbug [gui/send]
§c• Page §e[1/1]
§a---------------------
";
            
			if(!(isset($ar[0]) || isset($ar[1]))){
				$sender->sendMessage($msg);
				return true;
			}
			//foreach($this->getServer()->getOnlinePlayers() as $p){
			if($ar[0] == "send" || $ar[0] == "gui"){
				$this->baoCao($sender);
			}
			//}
			return true;
		}
		return true;
	}
	
	public function baoCao($sender){
		$api = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
		$form = $api->createCustomForm(Function (Player $sender, $data){
			$this->sr->set($sender->getName(), ["Lỗi" => $data[1], "Nhận Xét" => $data[2]]);
			$this->sr->save();
			if($sender->isOp()){
				$sender->sendMessage($this->tag . " §bCó người Gửi Báo Cáo OP ơi!");
			}else{
				$sender->sendMessage($this->tag . " §aGửi Báo Cáo Thành Công!");
			}
			foreach($this->getServer()->getOnlinePlayers() as $pl){
				$pl->sendPopup("§cCompleted!");
			}
		});
		$form->setTitle($this->tag);
		$form->addLabel("§a Diền Vào Ô sau:");
		$form->addInput("§a Điền Lỗi");
		$form->addInput("§a Nhận Xét");
		$form->sendToPlayer($sender);
	}
}