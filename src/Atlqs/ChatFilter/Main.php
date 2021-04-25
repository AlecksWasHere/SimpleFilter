<?php /** @noinspection PhpMissingFieldTypeInspection */

declare(strict_types=1);

namespace Atlqs\ChatFilter;
use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\utils\TextFormat as C;

class Main extends PluginBase implements Listener{

    private Config $mainConfig;
    private $chatCooldown;

    public function onEnable () : void{
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        @mkdir($this->getDataFolder());
        $this->mainConfig = new Config($this->getDataFolder() . "config.yaml", 2);
        $this->saveResource("config.yaml");
    }


public function onChat(PlayerChatEvent $event) {

        $cooldown = $this->mainConfig->get('cooldown');
        if(!$cooldown) $cooldown = 2;
        $player = $event->getPlayer();
        if(isset($this->chatCooldown[$player->getName()]) and time() - $this->chatCooldown[$player->getName()] < $cooldown) {
            ($this->chatCooldown[$player->getName()]) and time() - $this->chatCooldown[$player->getName()];
            $event->setCancelled();
            $player->sendMessage(C::RED . "- Slow down! You are sending many messages too quickly." . C::BLUE . " Cooldown: $cooldown(s).");

        } else {
            $this->chatCooldown[$player->getName()] = time();
            $message = $event->getMessage();
            $wordsArray = $this->mainConfig->get("words", []);
            foreach($wordsArray as $words) {
                $search = strpos($message, $words);
                if($search !== false) {

                    $characters = '#$&*!';
                    $charactersLength = strlen($words);
                    $randomString = "";
                    $length = strlen($message) - 1;
                    for ($i = 0; $i < $length; $i++) {
                        $randomString .= $characters[rand(0, $charactersLength - 1)];
                    }
                    $event->setCancelled();
                    $event->getPlayer()->sendMessage(C::RED . "- The chat filter has blocked one of the words you've tried to say.");
                    $event->getPlayer()->sendMessage(C::AQUA . "- The highlighted word(s) in yellow is/are blocked: " . str_replace("$words", C::YELLOW . "$randomString" . C::RESET, "$message"));
                }
            }
        }
    }
}
