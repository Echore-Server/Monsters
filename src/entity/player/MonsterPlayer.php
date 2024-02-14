<?php

declare(strict_types=1);

namespace Lyrica0954\Monsters\entity\player;

use Lyrica0954\Monsters\entity\MonsterBase;
use Lyrica0954\Monsters\entity\Motioner;
use Lyrica0954\Monsters\entity\state\StateManager;
use pocketmine\entity\Living;
use pocketmine\player\Player;
use pocketmine\utils\ObjectSet;
use RuntimeException;
use WeakMap;

class MonsterPlayer implements MonsterBase {

	/**
	 * @var WeakMap<Player, MonsterPlayer>
	 */
	private static WeakMap $map;

	protected Player $player;

	protected StateManager $states;

	protected Motioner $motioner;

	public function __construct(Player $player) {
		$this->player = $player;
		$this->states = new StateManager($player);
		$this->motioner = new Motioner($player);
	}

	public static function dispose(Player $player): void {
	}

	public static function get(Player $player): MonsterPlayer {
		self::$map ??= new WeakMap();

		return self::$map[$player] ??= self::load($player);
	}

	private static function load(Player $player): MonsterPlayer {
		return new self($player);
	}

	public function motion(): Motioner {
		return $this->motioner;
	}

	public function getStates(): StateManager {
		return $this->states;
	}

	/**
	 * @return void
	 *
	 * updater method: mainly called by Main plugin
	 */
	public function update(int $tickDiff): void {
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

	public function getAttackListeners(): ObjectSet {
		throw new RuntimeException("Not implemented");
	}

	public function getPreAttackListeners(): ObjectSet {
		throw new RuntimeException("Not implemented");
	}
}
