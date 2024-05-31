<?php

declare(strict_types=1);

namespace Lyrica0954\Monsters\entity\state;

use RuntimeException;

abstract class UpdatingState extends State {

	protected bool $flagForRemove = false;

	public function flagForRemove(): void {
		$this->flagForRemove = true;
	}

	public function isFlaggedForRemove(): bool {
		return $this->flagForRemove;
	}

	public function useBaseTick(): bool {
		return true;
	}

	public function baseTick(int $tickDiff = 1): void {
		if (!$this->applied) {
			throw new RuntimeException("cant update while deactivated");
		}

		$this->onUpdate($tickDiff);
	}

	abstract public function onUpdate(int $tickDiff = 1): void;
}
