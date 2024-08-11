<?php

declare(strict_types=1);

namespace Lyrica0954\Monsters\entity\state;

use Closure;
use Lyrica0954\Monsters\entity\MonsterBase;
use RuntimeException;

abstract class SchedulingState extends State {

	protected int $firstRunTick;

	protected ?int $repeatingTick;

	protected int $nextRunTick;

	private ?Closure $updateMediator;

	private ?int $internalNextRunTick;

	public function __construct(MonsterBase $monster, int $firstRunTick, ?int $repeatingTick) {
		parent::__construct($monster);
		$this->firstRunTick = $firstRunTick;
		$this->repeatingTick = $repeatingTick;
		$this->nextRunTick = $firstRunTick;
		$this->internalNextRunTick = null;
		$this->updateMediator = null;
	}

	/**
	 * @internal
	 */
	public function getInternalNextRunTick(): int {
		return $this->internalNextRunTick ?? throw new RuntimeException("internalNextRunTick not set");
	}

	/**
	 * @internal
	 */
	public function setInternalNextRunTick(int $internalNextRunTick): void {
		$this->internalNextRunTick = $internalNextRunTick;
	}

	public function getNextRunTick(): int {
		return $this->nextRunTick;
	}

	/**
	 * @param int $nextRunTick
	 */
	public function setNextRunTick(int $nextRunTick): void {
		$updated = $this->nextRunTick !== $nextRunTick;
		$this->nextRunTick = $nextRunTick;

		if ($updated) {
			$this->onScheduleChanged();
		}
	}

	protected function onScheduleChanged(): void {
		($this->updateMediator)();
	}

	/**
	 * @return int
	 */
	public function getFirstRunTick(): int {
		return $this->firstRunTick;
	}

	abstract public function onNotify(int $currentTick): void;

	public function getRepeatingTick(): ?int {
		return $this->repeatingTick;
	}

	public function setRepeatingTick(?int $repeatingTick): void {
		$this->repeatingTick = $repeatingTick;
	}

	/**
	 * @internal
	 */
	public function setUpdateMediator(Closure $closure): void {
		$this->updateMediator = $closure;
	}
}
