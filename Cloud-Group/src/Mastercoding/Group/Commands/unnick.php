<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 13.11.2018
 * Time: 15:07
 */

namespace Mastercoding\Group\Commands;

use Mastercoding\Group\Group;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\entity\Skin;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\Player;
use pocketmine\plugin\Plugin;

class unnick extends PluginCommand implements Listener {
    public $pl;

    public function __construct(Group $pl)
    {
        $this->pl = $pl;
        parent::__construct("unnick", $pl);
        $this->setDescription("§6unnick");

    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if ($sender instanceof Player) {
            if (Group::getInstance()->getEventListener()->isNicked($sender)) {
                $n = $sender->getName();
                Group::getInstance()->getEventListener()->setNick($sender, "n");
                $sender->sendMessage(Group::prefix . "§2Du bist nun unnickt");
            } else {
                $sender->sendMessage(Group::prefix . "§4Du bist nicht Genickt");
            }
        }
    }
}