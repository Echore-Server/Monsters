<?php

declare(strict_types=1);

namespace Lyrica0954\Monsters;

use Lyrica0954\Monsters\entity\player\MonsterPlayer;
use Lyrica0954\Monsters\timings\MonstersTimings;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\ClosureTask;
use pocketmine\Server;
use pocketmine\utils\SingletonTrait;

class Main extends PluginBase implements Listener {
	use SingletonTrait;

	public function onPlayerQuit(PlayerQuitEvent $event): void {
		$player = $event->getPlayer();

		MonsterPlayer::dispose($player);
	}

	protected function onLoad(): void {
		self::setInstance($this);
	}

	protected function onEnable(): void {
		$updateRate = 1;
		$this->getScheduler()->scheduleRepeatingTask(
			new ClosureTask(function() use ($updateRate): void {
				foreach (Server::getInstance()->getOnlinePlayers() as $player) {
					MonsterPlayer::get($player)->update($updateRate);
				}
			}), $updateRate
		);

		MonstersTimings::init();

		$this->getServer()->getPluginManager()->registerEvents($this, $this);
	}
}
