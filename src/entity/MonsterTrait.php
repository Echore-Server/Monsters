<?php

namespace Lyrica0954\Monsters\entity;

use Closure;
use Lyrica0954\Monsters\entity\state\StateManager;
use Lyrica0954\Monsters\Main;
use pocketmine\entity\Entity;
use pocketmine\entity\Living;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\scheduler\TaskScheduler;
use pocketmine\utils\ObjectSet;

trait MonsterTrait {

	protected StateManager $states;

	protected Motioner $motioner;

	protected ObjectSet $attackListeners;

	protected ObjectSet $preAttackListeners;

	protected TaskScheduler $monsterTaskScheduler;

	/**
	 * @return ObjectSet
	 */
	public function getAttackListeners(): ObjectSet {
		return $this->attackListeners;
	}

	/**
	 * @return ObjectSet<Closure(EntityDamageEvent): void>
	 */
	public function getPreAttackListeners(): ObjectSet {
		return $this->preAttackListeners;
	}

	public function hitEntity(Entity $entity, float $range): void {
		parent::hitEntity($entity, $range);

		$this->states->action("hitEntity");
	}

	/**
	 * Get the value of states
	 *
	 * @return StateManager
	 */
	public function getStates(): StateManager {
		return $this->states;
	}

	/**
	 * Get the value of motioner
	 *
	 * @return Motioner
	 */
	public function motion(): Motioner {
		return $this->motioner;
	}

	/**
	 * @param EntityDamageEvent $source
	 *
	 * @return void
	 *
	 * @notHandler
	 */
	public function attack(EntityDamageEvent $source): void {
		foreach ($this->preAttackListeners as $listener) {
			($listener)($source);
		}

		parent::attack($source);

		foreach ($this->attackListeners as $listener) {
			($listener)($source);
		}
	}

	public function getTaskScheduler(): TaskScheduler {
		return $this->monsterTaskScheduler;
	}

	protected function onDispose(): void {
		$this->states->dispose();
		parent::onDispose();
	}


	protected function initMonster(): void {
		assert($this instanceof MonsterBase);
		$this->states = new StateManager($this);
		$this->motioner = new Motioner($this->getEntity());
		$this->attackListeners = new ObjectSet();
		$this->preAttackListeners = new ObjectSet();
		$this->monsterTaskScheduler = Main::getInstance()->getScheduler();
	}

	public function getEntity(): Living {
		assert($this instanceof Living);

		return $this;
	}

	protected function entityBaseTick(int $tickDiff = 1): bool {
		assert($this instanceof Living);
		$hasUpdate = parent::entityBaseTick($tickDiff);

		if ($this->isAlive() && !$this->isFlaggedForDespawn()) {
			$this->states->update($tickDiff);
		}

		$this->monsterEntityBaseTick($tickDiff);

		return $hasUpdate;
	}

	protected function monsterEntityBaseTick(int $tickDiff = 1): void {

	}

	protected function destroyCycles(): void {
		parent::destroyCycles();
		unset($this->motioner, $this->states, $this->monsterTaskScheduler);
	}
}
