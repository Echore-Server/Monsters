<?php

declare(strict_types=1);

namespace Lyrica0954\Monsters\entity\state;

use Closure;
use Lyrica0954\Monsters\entity\MonsterBase;
use pocketmine\entity\Living;
use pocketmine\utils\ObjectSet;
use pocketmine\utils\ReversePriorityQueue;
use RuntimeException;

/**
 * 州じゃなくて状態ね
 */
class StateManager {

	protected MonsterBase $monster;

	/**
	 * @var State[]
	 */
	protected array $states;

	/**
	 * @var UpdatingState[]
	 */
	protected array $updatingStates;

	/**
	 * @var array<string, State[]>
	 */
	protected array $actionStates;

	/**
	 * @var array<string, State[]>
	 */
	protected array $stateByClass;

	/**
	 * @var ReversePriorityQueue<int, SchedulingState>
	 */
	protected ReversePriorityQueue $scheduledStates;

	protected bool $disposed;

	protected ObjectSet $applyListeners;

	/**
	 * @var array<string, ObjectSet>
	 */
	protected array $applyHooks;

	protected int $lastUpdatedTick;

	public function __construct(MonsterBase $monster) {
		$this->monster = $monster;
		$this->states = [];
		$this->actionStates = [];
		$this->updatingStates = [];
		$this->stateByClass = [];
		$this->lastUpdatedTick = $this->monster->getEntity()->getWorld()->getServer()->getTick();
		$this->scheduledStates = new ReversePriorityQueue();
		$this->disposed = false;
		$this->applyHooks = [];
		$this->applyListeners = new ObjectSet();
	}

	/**
	 * Get the value of entity
	 *
	 * @return Living
	 */
	public function getEntity(): Living {
		return $this->monster->getEntity();
	}

	/**
	 * @return MonsterBase
	 */
	public function getMonster(): MonsterBase {
		return $this->monster;
	}

	/**
	 * @return ObjectSet<Closure(State): void>
	 */
	public function getApplyListeners(): ObjectSet {
		return $this->applyListeners;
	}

	/**
	 * @param State $state
	 *
	 * @return bool
	 */
	public function apply(State $state): bool {
		if ($this->disposed) {
			throw new RuntimeException("state manager is already disposed");
		}

		if ($this->getEntity() !== $state->getEntity()) {
			throw new RuntimeException("(manager <-> state) entity does not match");
		}

		if ($state->isDisposed()) {
			throw new RuntimeException("the state is already disposed");
		}

		if ($state instanceof SchedulingState) {
			$this->scheduleState($state, $state->getNextRunTick());
		}

		$state->setApplied(true);
		$state->onApply();

		$this->states[spl_object_hash($state)] = $state;

		$this->stateByClass[$state::class][spl_object_hash($state)] = $state;

		if ($state instanceof UpdatingState && $state->useBaseTick()) {
			$this->updatingStates[spl_object_hash($state)] = $state;
		}

		foreach ($this->applyListeners as $listener) {
			($listener)($state);
		}

		foreach ($this->getApplyHooks($state::class) as $hook) {
			($hook)($state);
		}

		return true;
	}

	/**
	 * Get the value of disposed
	 *
	 * @return bool
	 */
	public function isDisposed(): bool {
		return $this->disposed;
	}

	/**
	 * @param SchedulingState $state
	 * @param int $tick
	 * @return void
	 * @internal
	 */
	public function scheduleState(SchedulingState $state, int $tick): void {
		$this->scheduledStates->insert($state, $tick);
		$state->setInternalNextRunTick($tick);

		$state->setUpdateMediator(function() use ($state): void {
			$this->onScheduleStateUpdate($state);
		});
	}

	public function onScheduleStateUpdate(SchedulingState $state): void {
		$currentTick = $this->monster->getEntity()->getWorld()->getServer()->getTick();
		if ($state->getNextRunTick() <= $currentTick) {
			$this->notify($state);
		}

		if ($state->getNextRunTick() < $state->getInternalNextRunTick()) {
			$this->scheduledStates->insert($state, $state->getNextRunTick());
			$state->setInternalNextRunTick($state->getNextRunTick());
		}
	}

	protected function notify(SchedulingState $state): void {
		$currentTick = $this->monster->getEntity()->getWorld()->getServer()->getTick();
		$state->onNotify($currentTick);

		if ($state->getRepeatingTick() !== null) {
			$state->setNextRunTick($state->getNextRunTick() + $state->getRepeatingTick());
			$state->setInternalNextRunTick($state->getNextRunTick());
			$this->scheduledStates->insert($state, $state->getNextRunTick() + $state->getRepeatingTick());
		} else {
			$this->remove($state);
		}
	}

	public function remove(State $state): void {
		if ($this->disposed) {
			throw new RuntimeException("state manager is already disposed");
		}

		if ($state->isDisposed()) {
			return;
		}

		unset($this->states[spl_object_hash($state)]);
		unset($this->updatingStates[spl_object_hash($state)]);
		unset($this->stateByClass[$state::class][spl_object_hash($state)]);

		foreach ($this->actionStates as $action => $states) {
			foreach ($states as $hash => $tstate) {
				if ($hash === spl_object_id($state)) {
					unset($this->actionStates[$action][$hash]);
				}
			}
		}

		$state->dispose();

		foreach ($state->getRemoveHooks() as $hook) {
			$hook($state);
		}
	}

	public function dispose(): void {
		$this->removeAll();

		$this->disposed = true;
	}

	public function removeAll(): void {
		foreach ($this->states as $state) {
			$this->remove($state);
		}
	}

	public function getApplyHooks(string $stateClass): ObjectSet {
		return $this->applyHooks[$stateClass] ??= new ObjectSet();
	}

	/**
	 * @return State[]
	 */
	public function getAll(): array {
		return $this->states;
	}

	public function removeAllOf(string $class): array {
		$states = $this->stateByClass[$class] ?? [];

		foreach ($states as $state) {
			$this->remove($state);
		}

		return $states;
	}

	public function has(string $class): bool {
		return count($this->getOf($class)) > 0;
	}

	/**
	 * @template T of State
	 * @param class-string<T> $class
	 * @return T[]
	 */
	public function getOf(string $class): array {
		return $this->stateByClass[$class] ?? [];
	}

	/**
	 * @template T of State
	 * @param class-string<T> $class
	 * @return T|null
	 */
	public function getOneOf(string $class): mixed {
		$states = $this->getOf($class);

		if (count($states) === 0) {
			return null;
		}

		return $states[array_key_first($states)];
	}

	/**
	 * @param State $target
	 *
	 * @return bool
	 *
	 * warning: この処理は O(n)
	 */
	public function hasEqual(State $target): bool {
		return count($this->fetchEquals($target)) > 0;
	}

	/**
	 * @param State $target
	 *
	 * @return array
	 *
	 * warning: この処理は O(n)
	 */
	public function fetchEquals(State $target): array {
		$states = [];
		foreach ($this->states as $state) {
			if ($target->equals($state)) {
				$states[] = $state;
			}
		}

		return $states;
	}

	public function action(string $action): void {
		if ($this->disposed) {
			throw new RuntimeException("state manager is already disposed");
		}

		foreach ($this->actionStates[$action] ?? [] as $state) {
			$state->$action();
		}
	}

	public function update(int $tickDiff = 1): void {
		$currentTick = $this->monster->getEntity()->getWorld()->getServer()->getTick();

		while (!$this->scheduledStates->isEmpty() && $this->scheduledStates->current()->getInternalNextRunTick() <= $currentTick) {
			$state = $this->scheduledStates->extract();

			if ($state->isDisposed()) {
				continue;
			}

			if ($state->getInternalNextRunTick() < $state->getNextRunTick()) {
				$this->scheduledStates->insert($state, $state->getNextRunTick());
				$state->setInternalNextRunTick($state->getNextRunTick());
			} else {
				$this->notify($state);
			}
		}

		foreach ($this->updatingStates as $state) {
			if ($state->isFlaggedForRemove()) {
				$this->remove($state);
				continue;
			}

			if (!$state->isApplied()) {
				continue;
			}

			$state->baseTick($tickDiff);
		}

		$this->lastUpdatedTick = $this->monster->getEntity()->getWorld()->getServer()->getTick();
	}
}
