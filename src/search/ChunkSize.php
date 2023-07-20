<?php

declare(strict_types=1);

namespace Lyrica0954\Monsters\search;

use pocketmine\network\mcpe\protocol\types\ChunkPosition;

class ChunkSize {

	public ChunkPosition $min;

	public ChunkPosition $max;

	public function __construct(ChunkPosition $min, ChunkPosition $max) {
		$this->min = $min;
		$this->max = $max;
	}
}
