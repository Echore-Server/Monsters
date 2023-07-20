<?php

declare(strict_types=1);

namespace Lyrica0954\Monsters\entity\source;

use Lyrica0954\Monsters\entity\MonsterDamageModifier;
use Lyrica0954\Monsters\utils\ValueModifier;
use pocketmine\event\entity\EntityDamageEvent;

class InboundDamageModifier {

	protected int $duration;

	protected int $add;

	protected float|int $multiplier;

	public function __construct(int $add, float $multiplier, int $duration = -1) {
		$this->add = $add;
		$this->multiplier = $multiplier;
		$this->duration = $duration;
	}

	public function tick(int $ticks = 1): bool {
		if ($this->duration === -1) {
			return true;
		}

		$this->duration -= $ticks;

		return $this->duration > 0;
	}

	public function apply(EntityDamageEvent $source): void {
		$value = $source->getModifier(MonsterDamageModifier::MULTIPLIER);

		$damage = $source->getFinalDamage();
		$adds = $damage * ($this->multiplier - 1.0);

		$source->setModifier(
			$value + $adds,
			MonsterDamageModifier::MULTIPLIER
		);

		$source->setModifier(
			$source->getModifier(MonsterDamageModifier::ADD) + $this->add,
			MonsterDamageModifier::ADD
		);
	}

	/**
	 * Get the value of duration
	 *
	 * @return int
	 */
	public function getDuration(): int {
		return $this->duration;
	}

	/**
	 * Set the value of duration
	 *
	 * @param int $duration
	 *
	 * @return self
	 */
	public function setDuration(int $duration): self {
		$this->duration = $duration;

		return $this;
	}
}
