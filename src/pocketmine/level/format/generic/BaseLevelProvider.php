<?php

/*
 *
 *  ____            _        _   __  __ _                  __  __ ____
 * |  _ \ ___   ___| | _____| |_|  \/  (_)_ __   ___      |  \/  |  _ \
 * | |_) / _ \ / __| |/ / _ \ __| |\/| | | '_ \ / _ \_____| |\/| | |_) |
 * |  __/ (_) | (__|   <  __/ |_| |  | | | | | |  __/_____| |  | |  __/
 * |_|   \___/ \___|_|\_\___|\__|_|  |_|_|_| |_|\___|     |_|  |_|_|
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author PocketMine Team
 * @link http://www.pocketmine.net/
 *
 *
*/

namespace pocketmine\level\format\generic;
use pocketmine\level\format\LevelProvider;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\nbt\NBT;
use pocketmine\nbt\tag\Compound;
use pocketmine\nbt\tag\Int;

abstract class BaseLevelProvider implements LevelProvider{
	/** @var Level */
	protected $level;
	/** @var string */
	protected $path;
	/** @var Compound */
	protected $levelData;

	public function __construct(Level $level, $path){
		$this->level = $level->getServer();
		$this->path = $path;
		@mkdir($this->path, 0777, true);
		$nbt = new NBT(NBT::BIG_ENDIAN);
		$nbt->readCompressed(file_get_contents($this->getPath() . "level.dat"));
		$levelData = $nbt->getData();
		if($levelData->Data instanceof Compound){
			$this->levelData = $levelData->Data;
		}else{
			throw new \Exception("Invalid level.dat");
		}
	}

	public function getPath(){
		return $this->path;
	}

	public function getServer(){
		return $this->level->getServer();
	}

	public function getLevel(){
		return $this->level;
	}

	public function getName(){
		return $this->levelData["LevelName"];
	}

	public function getTime(){
		return $this->levelData["Time"];
	}

	public function setTime($value){
		$this->levelData->Time = new Int("Time", (int) $value);
	}

	public function getSpawn(){
		return new Vector3($this->levelData["SpawnX"], $this->levelData["SpawnY"], $this->levelData["SpawnZ"]);
	}

	public function setSpawn(Vector3 $pos){
		$this->levelData->SpawnX = new Int("SpawnX", $pos->x);
		$this->levelData->SpawnY = new Int("SpawnY", $pos->y);
		$this->levelData->SpawnZ = new Int("SpawnZ", $pos->z);
	}

	/**
	 * @return Compound
	 */
	public function getLevelData(){
		return $this->levelData;
	}

	public function saveLevelData(){
		$nbt = new NBT(NBT::BIG_ENDIAN);
		$nbt->setData(new Compound(null, [
			"Data" => $this->levelData
		]));
		$buffer = $nbt->writeCompressed();
		@file_put_contents($this->getPath() . "level.dat", $buffer);
	}


}