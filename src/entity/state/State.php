<?php

declare(strict_types=1);

namespace Lyrica0954\Monsters\entity\state;

use Lyrica0954\Monsters\entity\MonsterBase;
use pocketmine\entity\Living;
use pocketmine\utils\ObjectSet;

abstract class State {

	protected MonsterBase $monster;

	protected Living $entity;

	protected bool $applied;

	protected bool $disposed;

	protected ObjectSet $removeHooks;

	public function __construct(MonsterBase $monster) {
		$this->monster = $monster;
		$this->entity = $monster->getEntity();
		$this->applied = false;
		$this->disposed = false;
		$this->removeHooks = new ObjectSet();
	}

	/**
	 * Get the value of entity
	 *
	 * @return Living
	 */
	public function getEntity(): Living {
		return $this->entity;
	}

	/**
	 * @return MonsterBase
	 */
	public function getMonster(): MonsterBase {
		return $this->monster;
	}

	abstract public function onApply(): void;

	public function conflicts(State $another): bool {
		return false;
	}

	public function shouldRemove(State $another): bool {
		return false;
	}

	public function equals(State $another): bool {
		return $this->entity === $another->getEntity() && static::class === $another::class;
	}

	public function dispose(): void {
		if ($this->applied) {
			$this->onRemove();
		}

		$this->applied = false;
		$this->disposed = true;
	}

	abstract public function onRemove(): void;


	/**
	 * @return ObjectSet
	 */
	public function getRemoveHooks(): ObjectSet {
		return $this->removeHooks;
	}

	/**
	 * Get the value of active
	 *
	 * @return bool
	 */
	public function isApplied(): bool {
		return $this->applied;
	}

	/**
	 * @param bool $applied
	 */
	public function setApplied(bool $applied): void {
		$this->applied = $applied;
	}

	/**
	 * Get the value of disposed
	 *
	 * @return bool
	 */
	public function isDisposed(): bool {
		return $this->disposed;
	}
}
