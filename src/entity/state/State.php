<?php

declare(strict_types=1);

namespace Lyrica0954\Monsters\entity\state;

use pocketmine\entity\Entity;
use pocketmine\entity\Living;
use pocketmine\utils\ObjectSet;

abstract class State {

	protected Living $entity;

	protected bool $active;

	protected bool $disposed;

	protected ObjectSet $removeHooks;

	public function __construct(Living $entity) {
		$this->entity = $entity;
		$this->active = false;
		$this->disposed = false;
		$this->removeHooks = new ObjectSet();
	}

	public function useHitEntity(): bool {
		return false;
	}

	public function hitEntity(Entity $entity, float $range): void {
	}

	abstract public function onApply(): void;

	public function conflicts(State $another): bool {
		return false;
	}

	public function equals(State $another): bool {
		return $this->entity === $another->getEntity() && static::class === $another::class;
	}

	/**
	 * Get the value of entity
	 *
	 * @return Living
	 */
	public function getEntity(): Living {
		return $this->entity;
	}

	public function dispose(): void {
		if ($this->active) {
			$this->onRemove();
		}

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
	public function isActive(): bool {
		return $this->active;
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
