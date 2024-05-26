<?php
declare(strict_types=1);

namespace Azel\CustomNotif;

use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\Config;
use pocketmine\event\Listener;
use pocketmine\permission\DefaultPermissions;
use pocketmine\permission\Permission;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerDeathEvent;

use jojoe77777\FormAPI;
use jojoe77777\FormAPI\SimpleForm;
use jojoe77777\FormAPI\CustomForm;
class Main extends PluginBase implements Listener{
  
  public function onEnable(): void{
    @mkdir($this->getDataFolder());
		$this->saveDefaultConfig();
		$this->saveResource("config.yml");
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->getMessage = new Config($this->getDataFolder() . "config.yml", Config::YAML);

  }
  
  public function onJoin(PlayerJoinEvent $ev){
    $player = $ev->getPlayer();
    $pname = $ev->getPlayer()->getName();
    $message = str_replace(["{player}", "{line}"], [$pname, "\n"], $this->getConfig()->get("join-message"));
    $title = str_replace(["{player}", "{line}"], [$pname, "\n"], $this->getConfig()->get("join-title"));
    $ev->setJoinMessage($message);
    if($this->getConfig()->get("enable-join-title", "true")){
      $player->sendTitle($title);
    }
  }
  
  public function onDeath(PlayerDeathEvent $ev){
    $pname = $ev->getPlayer();
    $message = str_replace(["{player}", "{line}"], [$pname, "\n"], $this->getConfig()->get("join-message"));
    $ev->setDeathMessage($message);
  }
  
  public function onQuit(PlayerQuitEvent $ev){
    $pname = $ev->getPlayer()->getName();
    $message = str_replace(["{player}", "{line}"], [$pname, "\n"], $this->getConfig()->get("quit-message"));
    $ev->setQuitMessage($message);
  }
  
  public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args): bool{
    if($cmd->getName() === "customnotif"){
			if(!$sender instanceof Player){
				$sender->sendMessage("§7[§6Custom Notif§7] §cPlease run this command in-game.");
			}else{
				if ($sender->hasPermission("customnotif.edit")) {
					$this->FormEdit($sender);
				}else{
					$sender->sendMessage("§7[§6Custom Notif§7] " . "§cYou don't have permission for use this command!");
				}
			}
    }
   return true;
  }
  
  public function FormEdit($player){
    $form = new SimpleForm(function (Player $player, $data = null){					
			if($data === null){
				return true;
			}else{
				switch($data){
					case "0":
					  $this->editJoin($player);
						break;
					case "1":
						$this->editJoin($player);
						break;
					case "2":
					  $this->editJoin($player);
					  break;
					case "3":
					  $this->editTitle($player);
					  break;
					  break;
				}
			}
    });
		$form->setTitle("§l§bEdit Custom Notif");
		$form->addButton("§l§aJoin Message\n§rClick For Editing");
		$form->addButton("§l§aDeath Message\n§rClick For Editing");
		$form->addButton("§l§aQuit Message\n§rClick For Editing");
		$form->addButton("§l§aJoin Title\n§rClick For Editing");
    $form->addButton("§l§cEXIT");
	  $player->sendForm($form);
			return $form;
  }
  
  public function editJoin($player){
    $form = new CustomForm(function (Player $player, ?array $data): void{
      if($data !== null){
        $this->getConfig()->set("join-message", $data[0]);
        $this->getConfig()->save;
        $player->sendMessage("§aJoin message has been changed to $data[0]");
      }
      $this->FormEdit($player);
    });
    $form->setTitle("§l§bEDIT JOIN NOTIF");
    $form->addInput("Input Text");
    $player->sendForm($form);
    return $form;
    } 
  
  public function editDeath($player){
    $form = new CustomForm(function (Player $player, ?array $data): void{
      if($data !== null){
        $this->getConfig()->set("death-message", $data[0]);
        $this->getConfig()->save;
        $player->sendMessage("§aDeath message has been changed to $data[0]");
      }
      $this->FormEdit($player);
    });
    $form->setTitle("§l§bEDIT DEATH NOTIF");
    $form->addInput("Input Text");
    $player->sendForm($form);
    return $form;
  }
  
  public function editQuit($player){
    $form = new CustomForm(function (Player $player, ?array $data): void{
      if($data !== null){
        $this->getConfig()->set("quit-message", $data[0]);
        $this->getConfig()->save;
        $player->sendMessage("§aQuit message has been changed to $data[0]");
      }
      $this->FormEdit($player);
    });
    $form->setTitle("§l§bEDIT QUIT NOTIF");
    $form->addInput("Input Text");
    $player->sendForm($form);
    return $form;
  } 
  
  
  
  public function editTitle($player){
      $form = new CustomForm(function (Player $player, ?array $data): void{
        if($data !== null){
          $this->getConfig()->set("join-title", $data[0]);
          $this->getConfig()->save;
          $player->sendMessage("§aJoin Title has been changed to $data[0]");
        }
        $this->getConfig()->set("enable-join-title", $data[1]);
      });
      $form->setTitle("§l§bEDIT JOIN TITLE");
      $form->addInput("Input Text");
      $form->addToggle("Enable", true);
      $player->sendForm($form);
      return $form;
      } 
    
} 