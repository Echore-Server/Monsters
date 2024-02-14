<?php

declare(strict_types=1);

namespace Lyrica0954\Monsters\entity\state;

use pocketmine\entity\Living;

abstract class DurationState extends UpdatingState {

	protected int $duration;

	protected int $remainTick;

	protected bool $useBaseTick;

	public function __construct(Living $entity, int $duration) {
		parent::__construct($entity);
		$this->duration = $duration;
		$this->remainTick = 0;
		$this->useBaseTick = $duration > 0;
	}

	public function useBaseTick(): bool {
		return $this->useBaseTick;
	}

	public function onUpdate(int $tickDiff = 1): void {
		$this->remainTick -= $tickDiff;

		if ($this->remainTick <= 0) {
			$this->flagForRemove();
		}
	}

	public function onApply(): void {
		$this->remainTick = $this->duration;
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
	 * @param int $remainTick
	 */
	public function setRemainTick(int $remainTick): void {
		$this->remainTick = min($remainTick, $this->duration);

		if ($this->remainTick <= 0){
			$this->flagForRemove();
		}
	}

	/**
	 * @param int $duration
	 */
	public function setDuration(int $duration): void {
		$this->duration = $duration;
		$this->useBaseTick = $duration > 0;
	}

	/**
	 * Get the value of remainTick
	 *
	 * @return int
	 */
	public function getRemainTick(): int {
		return $this->remainTick;
	}
}
