<?php

declare(strict_types=1);

namespace Lyrica0954\Monsters\entity\state;

use Closure;
use Lyrica0954\Monsters\entity\MonsterBase;
use pocketmine\entity\Living;
use pocketmine\Server;
use pocketmine\utils\ObjectSet;
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
	protected array $stateByClass;

	/**
	 * @var array<int, SchedulingState[]>
	 */
	protected array $scheduledStates;

	protected bool $disposed;

	protected ObjectSet $applyListeners;

	/**
	 * @var array<string, ObjectSet>
	 */
	protected array $applyHooks;

	protected int $lastUpdatedTick;

	protected int $scheduledStateCount;

	public function __construct(MonsterBase $monster) {
		$this->monster = $monster;
		$this->states = [];
		$this->updatingStates = [];
		$this->stateByClass = [];
		$this->lastUpdatedTick = $this->monster->getEntity()->getWorld()->getServer()->getTick();
		$this->scheduledStates = [];
		$this->disposed = false;
		$this->applyHooks = [];
		$this->scheduledStateCount = 0;
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

		$targetHash = spl_object_hash($state);

		$state->setApplied(true);
		$state->onApply();
		$this->states[$targetHash] = $state;

		$this->stateByClass[$state::class][$targetHash] = $state;

		if ($state instanceof UpdatingState && $state->useBaseTick()) {
			$this->updatingStates[$targetHash] = $state;
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
		$this->insertToScheduledStates($state, $tick);
		$state->setInternalNextRunTick($tick);

		$state->setUpdateMediator(function() use ($state): void {
			$this->onScheduleStateUpdate($state);
		});
	}

	protected function insertToScheduledStates(SchedulingState $state, int $tick): void {
		$this->scheduledStates[$tick][spl_object_hash($state)] = $state;
		$this->scheduledStateCount++;
	}

	public function onScheduleStateUpdate(SchedulingState $state): void {
		$currentTick = $this->monster->getEntity()->getWorld()->getServer()->getTick();
		if ($state->getNextRunTick() <= $currentTick) {
			$this->removeFromScheduledStates($state, $state->getInternalNextRunTick());
			$this->notify($state);

			return;
		}

		if ($state->getNextRunTick() !== $state->getInternalNextRunTick()) {
			$this->removeFromScheduledStates($state, $state->getInternalNextRunTick());
			$this->insertToScheduledStates($state, $state->getNextRunTick());

			$state->setInternalNextRunTick($state->getNextRunTick());
		}
	}

	protected function removeFromScheduledStates(SchedulingState $state, int $tick): void {
		if (isset($this->scheduledStates[$tick][spl_object_hash($state)])) {
			$this->scheduledStateCount--;
		}
		unset($this->scheduledStates[$tick][spl_object_hash($state)]);
	}

	protected function notify(SchedulingState $state): void {
		$currentTick = $this->monster->getEntity()->getWorld()->getServer()->getTick();
		$state->onNotify($currentTick);

		if ($state->getRepeatingTick() !== null) {
			$state->setNextRunTick($state->getNextRunTick() + $state->getRepeatingTick());
			$state->setInternalNextRunTick($state->getNextRunTick());
			$this->insertToScheduledStates($state, $state->getNextRunTick());
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

		$targetHash = spl_object_hash($state);

		if ($state instanceof SchedulingState) {
			$this->removeFromScheduledStates($state, $state->getInternalNextRunTick());
		}

		unset($this->states[$targetHash]);
		unset($this->updatingStates[$targetHash]);
		unset($this->stateByClass[$state::class][$targetHash]);

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
		return !empty($this->getOf($class));
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

		if (empty($states)) {
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

	public function update(int $tickDiff = 1): void {
		$currentTick = Server::getInstance()->getTick();

		if ($this->scheduledStateCount > 0) {
			for ($tick = $this->lastUpdatedTick + 1; $tick <= $currentTick; $tick++) {
				foreach ($this->scheduledStates[$tick] ?? [] as $state) {
					if ($state->isDisposed()) {
						continue;
					}
					$this->notify($state);
				}
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

		$this->lastUpdatedTick = $currentTick;
	}
}
