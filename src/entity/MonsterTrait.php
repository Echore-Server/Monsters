<?php

namespace Lyrica0954\Monsters\entity;

use Echore\StarProject\entity\source\MonsterDamageEvent;
use Echore\StarProject\entity\source\MonsterDamageSource;
use Echore\StarProject\game\attribute\DefaultDamageAttributes;
use Lyrica0954\Monsters\entity\state\StateManager;
use Lyrica0954\Monsters\Main;
use pocketmine\block\Lava;
use pocketmine\entity\Living;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\scheduler\TaskScheduler;
use pocketmine\utils\ObjectSet;

trait MonsterTrait {

	protected StateManager $states;

	protected Motioner $motioner;

	protected TaskScheduler $monsterTaskScheduler;

	protected ObjectSet $attackCaptures;


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

	public function getTaskScheduler(): TaskScheduler {
		return $this->monsterTaskScheduler;
	}

	public function applyDamageModifiers(EntityDamageEvent $source): void {
	}

	protected function onDispose(): void {
		$this->states->dispose();
		parent::onDispose();
	}

	protected function initMonster(): void {
		assert($this instanceof MonsterBase);
		$this->states = new StateManager($this);
		$this->motioner = new Motioner($this->getEntity());
		$this->monsterTaskScheduler = Main::getInstance()->getScheduler();
		$this->attackCaptures = new ObjectSet();
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

			if ($this->getWorld()->getBlock($this->getPosition()) instanceof Lava) {
				$source = new MonsterDamageEvent(
					$this,
					EntityDamageEvent::CAUSE_LAVA,
					MonsterDamageSource::NATURAL | MonsterDamageSource::STATIC,
					$this->getMaxHealth() * 0.01 * $tickDiff,
					DefaultDamageAttributes::FLAME()
				);

				$this->attack($source);
			}
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
