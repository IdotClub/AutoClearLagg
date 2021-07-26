<?php
declare(strict_types=1);

namespace twisted\autoclearlagg;

use pocketmine\entity\Ageable;
use pocketmine\entity\Human;
use pocketmine\entity\Living;
use pocketmine\entity\object\ExperienceOrb;
use pocketmine\entity\object\ItemEntity;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\ClosureTask;
use pocketmine\utils\AssumptionFailedError;
use Webmozart\PathUtil\Path;
use function in_array;
use function str_replace;
use function strtolower;

class AutoClearLagg extends PluginBase {
	public int $seconds;
	private ConfigBean $bean;

	public function onEnable() : void {
		$this->saveResource("config.json");
		try {
			$mapper = new \JsonMapper();
			$mapper->bIgnoreVisibility = true;
			$mapper->bExceptionOnUndefinedProperty = true;
			$mapper->bExceptionOnMissingData = true;
			$bean = $mapper->map(json_decode(
				file_get_contents(Path::join($this->getDataFolder(), "config.json")),
				false, 512, JSON_THROW_ON_ERROR
			), new ConfigBean());
			if (!$bean instanceof ConfigBean) {
				throw new AssumptionFailedError("JsonMapper should return ConfigBean back");
			}
			$this->bean = $bean;
		} catch (\Throwable $e) {
			$this->getLogger()->error("Error when parsing(mapping) config.json, please check config.json");
			$this->getLogger()->error(get_class($e) . ": {$e->getMessage()}");
			return;
		}
		$this->seconds = $this->bean->getSeconds();
		$this->getScheduler()->scheduleRepeatingTask(new ClosureTask(function () : void {
			$bean = $this->bean->getClearSettings();
			$messageBean = $this->bean->getMessages();
			if (--$this->seconds === 0) {
				$entitiesCleared = 0;
				foreach ($this->getServer()->getWorldManager()->getWorlds() as $level) {
					foreach ($level->getEntities() as $entity) {
						if ($entity instanceof ItemEntity && $bean->isItemsEnabled()) {
							$entity->flagForDespawn();
							++$entitiesCleared;
						}

						if (
							$entity instanceof Living &&
							$entity instanceof Ageable &&
							!$entity instanceof Human &&
							$bean->isMobsEnabled() &&
							!in_array(strtolower($entity->getName()), $bean->getExemptList(), true)
						) {
							$entity->flagForDespawn();
							++$entitiesCleared;
						}

						if ($entity instanceof ExperienceOrb && $bean->isXpOrbsEnabled()) {
							$entity->flagForDespawn();
							++$entitiesCleared;
						}
					}
				}
				if ($messageBean->getEntitiesClearedFormat() !== "") {
					$this->getServer()->broadcastMessage(str_replace("{COUNT}", (string) $entitiesCleared, $messageBean->getEntitiesClearedFormat()));
				}
				$this->seconds = $this->bean->getSeconds();
			} elseif ($messageBean->getTimeLeftFormat() !== "" && in_array($this->seconds, $this->bean->getDisplayTimes(), true)) {
				$this->getServer()->broadcastMessage(str_replace("{SECONDS}", (string) $this->seconds, $messageBean->getTimeLeftFormat()));
			}
		}), 20);
	}
}
