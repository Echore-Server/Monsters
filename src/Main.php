<?php

declare(strict_types=1);

namespace Lyrica0954\Monsters;

use Lyrica0954\Monsters\entity\player\MonsterPlayer;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\ClosureTask;
use pocketmine\Server;

class Main extends PluginBase {

	protected function onEnable(): void {
		$updateRate = 1;
		$this->getScheduler()->scheduleRepeatingTask(
			new ClosureTask(function() use ($updateRate): void {
				foreach (Server::getInstance()->getOnlinePlayers() as $player) {
					MonsterPlayer::get($player)->update($updateRate);
				}
			}), $updateRate);
	}
}
