<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 16.12.2018
 * Time: 17:00
 */

namespace Mastercoding\Group\Commands;
use Mastercoding\Group\Group;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\network\mcpe\protocol\ModalFormRequestPacket;
use pocketmine\Player;
use pocketmine\Server;

class Realname extends Command implements Listener {
    protected $pl;

    public function __construct(Group $pl, string $name, string $description = "", string $usageMessage = null, $aliases = [], array $overloads = null)
    {
        $this->pl = $pl;
        parent::__construct($name, $description, $usageMessage, $aliases, $overloads);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
     if ($sender instanceof Player){
         $players = Server::getInstance()->getOnlinePlayers();
         $array = [];
         foreach ($players as $player){
             if (Group::getInstance()->getEventListener()->isNicked($player) === true){
                 $array[] = $player;
             }
         }
         $this->realNameList($sender, $array);
     }
    }


    public function realNameList(Player $player, array $array){
        $fdata = [];


        $fdata['title'] = "§7--- §5Realnames §7---";
        $fdata['buttons'] = [];
        $fdata['content'] = '';
        $fdata['type'] = 'form';

        foreach ($array as $player){
            $fdata['buttons'][] = ['text' => "§2{$player->getName()}§7/§b{$player->getDisplayName()}"];
        }

        $pk = new ModalFormRequestPacket();
        $pk->formId = 78002;
        $pk->formData = json_encode($fdata);
        $player->sendDataPacket($pk);
    }
}