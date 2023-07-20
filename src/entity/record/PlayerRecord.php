<?php

declare(strict_types=1);

namespace Lyrica0954\Monsters\entity\record;

use pocketmine\player\Player;

abstract class PlayerRecord {

	protected array $records = [];

	public function getAll(): array {
		return $this->records;
	}

	abstract public function get(Player $player);
}
