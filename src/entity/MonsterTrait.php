<?php

namespace Lyrica0954\Monsters\entity;

use Lyrica0954\Monsters\entity\record\FloatPlayerRecord;
use Lyrica0954\Monsters\entity\source\ContinuousDamageEvent;
use Lyrica0954\Monsters\entity\source\InboundDamageModifierEvent;
use Lyrica0954\Monsters\entity\state\StateManager;
use pocketmine\entity\Entity;
use pocketmine\entity\Living;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\player\Player;
use pocketmine\utils\ObjectSet;

trait MonsterTrait {

	protected StateManager $states;

	protected FloatPlayerRecord $inboundDamageRecord;

	protected Motioner $motioner;

	protected ObjectSet $attackListeners;

	/**
	 * @return ObjectSet
	 */
	public function getAttackListeners(): ObjectSet {
		return $this->attackListeners;
	}

	public function hitEntity(Entity $entity, float $range): void {
		parent::hitEntity($entity, $range);

		$this->states->action("hitEntity");
	}

	/**
	 * Get the value of inboundDamageRecord
	 *
	 * @return FloatPlayerRecord
	 */
	public function getInboundDamageRecord(): FloatPlayerRecord {
		return $this->inboundDamageRecord;
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
	 * Get the value of continuousDamages
	 *
	 * @return array
	 */
	public function getContinuousDamages(): array {
		return $this->continuousDamages;
	}

	/**
	 * Get the value of inboundDamageModifiers
	 *
	 * @return array
	 */
	public function getInboundDamageModifiers(): array {
		return $this->inboundDamageModifiers;
	}

	/**
	 * Get the value of motioner
	 *
	 * @return Motioner
	 */
	public function motion(): Motioner {
		return $this->motioner;
	}

	public function getEntity(): Living {
		return $this;
	}

	/**
	 * @param EntityDamageEvent $source
	 *
	 * @return void
	 *
	 * @notHandler
	 */
	public function attack(EntityDamageEvent $source): void {
		parent::attack($source);

		foreach ($this->attackListeners as $listener) {
			($listener)($source);
		}

		if ($source instanceof EntityDamageByEntityEvent) {
			if (($damager = $source->getDamager()) instanceof Player) {
				$this->inboundDamageRecord->add($damager, $source->getFinalDamage());
			}
		}
	}

	protected function initMonster(): void {
		$this->states = new StateManager($this);
		$this->motioner = new Motioner($this);
		$this->inboundDamageRecord = new FloatPlayerRecord();
		$this->attackListeners = new ObjectSet();
	}

	protected function onDispose(): void {
		parent::onDispose();

		$this->states->dispose();
	}

	protected function entityBaseTick(int $tickDiff = 1): bool {
		$hasUpdate = parent::entityBaseTick($tickDiff);

		if ($this->isAlive() && !$this->isFlaggedForDespawn()) {
			$this->states->update($tickDiff);
		}

		return $hasUpdate;
	}
}
