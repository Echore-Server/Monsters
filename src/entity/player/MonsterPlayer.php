<?php

declare(strict_types=1);

namespace Lyrica0954\Monsters\entity\player;

use Lyrica0954\Monsters\entity\MonsterBase;
use Lyrica0954\Monsters\entity\Motioner;
use Lyrica0954\Monsters\entity\source\ContinuousDamageEvent;
use Lyrica0954\Monsters\entity\source\InboundDamageModifierEvent;
use Lyrica0954\Monsters\entity\state\StateManager;
use Lyrica0954\PocketEvent\Globals;
use Lyrica0954\PocketEvent\PlayerCastableEventWrapper;
use Lyrica0954\PocketEvent\register\PlayerEventRegister;
use pocketmine\entity\Living;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\player\Player;
use WeakMap;

class MonsterPlayer implements MonsterBase {

	/**
	 * @var WeakMap<Player, MonsterPlayer>
	 */
	private static WeakMap $map;

	protected Player $player;

	/**
	 * @var ContinuousDamageEvent[]
	 */
	protected array $continuousDamages = [];

	/**
	 * @var InboundDamageModifierEvent[]
	 */
	protected array $inboundDamageModifiers = [];

	/**
	 * @var int[]
	 */
	protected array $lastInboundDamageModified = [];

	protected StateManager $states;

	protected Motioner $motioner;

	protected PlayerEventRegister $eventRegister;

	public function __construct(Player $player) {
		$this->player = $player;
		$this->states = new StateManager($player);
		$this->motioner = new Motioner($player);

		$this->eventRegister = Globals::eventEmitter()->player()->on(
			new PlayerEventRegister($player->getUniqueId()->toString(), EntityDamageEvent::class, function(EntityDamageEvent $source, Player $player): void {

				foreach ($this->inboundDamageModifiers as $index => $event) {
					$modifier = $event->getDamageModifier();
					$elapsed = $this->player->ticksLived - $this->lastInboundDamageModified[spl_object_hash($event)];
					if ($modifier->tick($elapsed)) {
						$modifier->apply($source);

						$this->lastInboundDamageModified[spl_object_hash($event)] = $this->player->ticksLived;
					} else {
						unset($this->inboundDamageModifiers[$index]);
						unset($this->lastInboundDamageModified[spl_object_hash($event)]);
					}
				}
			}));
	}

	public static function get(Player $player): MonsterPlayer {
		self::$map ??= new WeakMap();

		return self::$map[$player] ??= self::load($player);
	}

	private static function load(Player $player): MonsterPlayer {
		return new self($player);
	}

	public function __destruct() {
		Globals::eventEmitter()->player()->off($this->eventRegister);
	}

	public function applyContinuousDamage(ContinuousDamageEvent $source): void {
		$source->call();

		if ($source->isCancelled()) {
			return;
		}

		$this->continuousDamages[] = $source;
	}

	public function applyInboundDamageModifier(InboundDamageModifierEvent $source): void {
		$source->call();

		if ($source->isCancelled()) {
			return;
		}

		$this->inboundDamageModifiers[] = $source;
		$this->lastInboundDamageModified[spl_object_hash($source)] = $this->player->ticksLived;
	}

	public function getContinuousDamages(): array {
		return $this->continuousDamages;
	}

	public function motion(): Motioner {
		return $this->motioner;
	}

	public function getStates(): StateManager {
		return $this->states;
	}

	public function getInboundDamageModifiers(): array {
		return $this->inboundDamageModifiers;
	}

	/**
	 * @return void
	 *
	 * updater method: mainly called by Main plugin
	 */
	public function update(int $tickDiff): void {
		foreach ($this->continuousDamages as $index => $event) {
			$continuousDamage = $event->getContinuousDamage();
			$count = $continuousDamage->tick($tickDiff);
			for ($i = 0; $i < $count; $i++) {
				$source = $event->source();
				$source->setAttackCooldown(0);

				$this->player->attack($source);
			}

			if ($continuousDamage->getCount() <= 0) {
				unset($this->continuousDamages[$index]);
			}
		}

		$this->states->update($tickDiff);
	}

	/**
	 * @return Living
	 */
	public function getEntity(): Living {
		return $this->getPlayer();
	}

	/**
	 * @return Player
	 */
	public function getPlayer(): Player {
		return $this->player;
	}
}
