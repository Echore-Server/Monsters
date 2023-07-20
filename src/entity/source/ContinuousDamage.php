<?php

declare(strict_types=1);

namespace Lyrica0954\Monsters\entity\source;

class ContinuousDamage {

	protected float $damage;

	protected int $period;

	protected int $count;

	protected int $ticks;

	public function __construct(float $damage, int $period, int $count) {
		$this->damage = $damage;
		$this->period = $period;
		$this->count = $count;
		$this->ticks = 0;
	}

	public static function fromDuration(float $damage, int $period, int $duration): self {
		return new self($damage, $period, (int) ($duration / $period));
	}

	public function tick(int $ticks = 1, int $count = 0): int {
		$this->ticks -= $ticks;
		if ($this->ticks <= 0) {
			$result = $this->tick(-$this->period, $count++);
			$this->count--;

			return $result;
		} else {
			return $count;
		}
	}

	/**
	 * Get the value of damage
	 *
	 * @return float
	 */
	public function getDamage(): float {
		return $this->damage;
	}

	/**
	 * Set the value of damage
	 *
	 * @param float $damage
	 *
	 * @return self
	 */
	public function setDamage(float $damage): self {
		$this->damage = $damage;

		return $this;
	}

	/**
	 * Get the value of period
	 *
	 * @return int
	 */
	public function getPeriod(): int {
		return $this->period;
	}

	/**
	 * Set the value of period
	 *
	 * @param int $period
	 *
	 * @return self
	 */
	public function setPeriod(int $period): self {
		$this->period = $period;

		return $this;
	}

	/**
	 * Get the value of count
	 *
	 * @return int
	 */
	public function getCount(): int {
		return $this->count;
	}

	/**
	 * Set the value of count
	 *
	 * @param int $count
	 *
	 * @return self
	 */
	public function setCount(int $count): self {
		$this->count = $count;

		return $this;
	}

	/**
	 * Get the value of ticks
	 *
	 * @return int
	 */
	public function getTicks(): int {
		return $this->ticks;
	}
}
