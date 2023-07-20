<?php

declare(strict_types=1);

namespace Lyrica0954\Monsters\entity\record;

use pocketmine\player\Player;

class FloatPlayerRecord extends PlayerRecord {

	public function add(Player $player, float $value): float {
		$v = $this->get($player);
		$this->set($player, $v + $value);

		return $v;
	}

	public function get(Player $player): float {
		return $this->records[$player->getUniqueId()->toString()] ?? 0.0;
	}

	public function set(Player $player, float $value): void {
		$this->records[$player->getUniqueId()->toString()] = $value;
	}
}
