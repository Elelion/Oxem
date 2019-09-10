<?php

interface Animal {}

interface CanGiveMilk {
    public function getMilk(): int;
}

interface CanGiveEggs {
    public function getEggs(): int;
}

// **

class Cows implements Animal, CanGiveMilk {
	public $id;

	public function __construct()
	{
		// NOTE: получаем случаный id длинною в 6 символов
		$this->id = substr(md5(rand()), 0, 6);
	}

	public function getMilk(): int
	{
		// NOTE: выдает 8-12 литров молока
		return rand(8, 12);
	}
}

// **

class Chickens implements Animal, CanGiveEggs {
	public $id;
	public function __construct()
	{
		// NOTE: получаем случаный id длинною в 6 символов
		$this->id = substr(md5(rand()), 0, 6);
	}

	public function getEggs(): int
	{
		// NOTE: выдает 0-1 яиц
		return rand(0, 1);
	}
}

// **

interface Storage {
	public function addMilk(int $liters);
	public function addEggs(int $eggsCount);
	public function getFreeSpaceForMilk(): int;
	public function getFreeSpaceForEggs(): int;
	public function howMuchMilk(): int;
	public function howMuchEggs(): int;
}

// **

class Warehouse implements Storage {
	private $milkLiters = 0;
	private $eggsCount = 0;
	private $milkLimit = 0;
	private $eggsLimit = 0;

	public function __construct(int $milkLimit, int $eggsLimit)
	{
		$this->milkLimit = $milkLimit;
		$this->eggsLimit = $eggsLimit;
	}

	// **

	public function addMilk(int $liters)
	{
		$freeSpace = $this->getFreeSpaceForMilk();

		// NOTE: абмар заполнен, места нет
		if ($freeSpace === 0) {
			return;
		}

		// NOTE: дозаполняем амбар, насколько хватает места
		if ($freeSpace < $liters) {
			$this->milkLiters = $this->milkLimit;
			return;
		}

		// NOTE: льем все молоко, что надоили
		$this->milkLiters += $liters;
	}

	public function addEggs(int $eggsCount)
	{
		$freeSpace = $this->getFreeSpaceForEggs();

		if ($freeSpace === 0) {
			return;
		}

		if ($freeSpace < $eggsCount) {
			$this->eggsCount = $this->eggsLimit;
			return;
		}

		$this->eggsCount += $eggsCount;
	}

	// **

	// NOTE: считаем свободное места
	public function getFreeSpaceForMilk(): int
	{
		return $this->milkLimit - $this->milkLiters;
	}

	public function getFreeSpaceForEggs(): int
	{
		return $this->eggsLimit - $this->eggsCount;
	}

	// **

	public function howMuchMilk(): int
	{
		return $this->milkLiters;
	}

	public function howMuchEggs(): int
	{
		return $this->eggsCount;
	}
}

// **

class Farm {
	private $name;
	private $storage;
	private $animals = [];

	public function __construct(string $name, Storage $storage)
	{
		$this->name = $name;
		$this->storage = $storage;
	}

	public function returnMilk()
	{
		return $this->storage->howMuchMilk();
	}

	public function returnEggs()
	{
		return $this->storage->howMuchEggs();
	}

	// NOTE: добавляем животное в массив
	public function addAnimal(Animal $animal)
	{
		$this->animals[] = $animal;
	}

	// **

	public function collectProducts()
	{
		foreach ($this->animals as $animal)
		{
			// NOTE: если молоко, то сбор молока
			if ($animal instanceOf CanGiveMilk) {
				$milkLiters = $animal->getMilk();
				$this->storage->addMilk($milkLiters);
			}

			if ($animal instanceOf CanGiveEggs) {
				$eggsCount = $animal->getEggs();
				$this->storage->addEggs($eggsCount);
			}
		}
	}
}

// **

// NOTE: создаем амбар вместимостью 250 литров молока и 300 яиц
$warehouse = new Warehouse($milkLimit = 250, $eggsLimit = 300);

$myFarm = new Farm('MyFirstFarm', $warehouse);

// NOTE: заселяем куриц
for ($i=0; $i<40; $i++) {
	$myFarm->addAnimal(new Chickens());
}

// NOTE: заселяем коров
for ($i=0; $i<10; $i++) {
	$myFarm->addAnimal(new Cows());
}

// NOTE: собираем все что есть
$myFarm->collectProducts();

echo 'Молока надоено '.$myFarm->returnMilk().'<br>';
echo 'Яиц собрано '.$myFarm->returnEggs().'<br>';
