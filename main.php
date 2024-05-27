<?php

require 'vendor/autoload.php';

use Whoops\Run;
use Whoops\Handler\PrettyPageHandler;
use Respect\Validation\Validator as v;

$whoops = new Run();
$whoops->pushHandler(new PrettyPageHandler());
$whoops->register();

class Animal
{
    private $name;
    private $happiness;
    private $foodType;
    private $foodReserves;

    public function __construct($name, $foodType)
    {
        $this->name = $name;
        $this->happiness = 100;
        $this->foodType = $foodType;
        $this->foodReserves = 100;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getHappiness()
    {
        return $this->happiness;
    }

    public function getFoodReserves()
    {
        return $this->foodReserves;
    }

    public function play()
    {
        $this->happiness += 20;
        $this->foodReserves -= 20;
    }

    public function work()
    {
        $this->happiness -= 20;
        $this->foodReserves -= 20;
    }

    public function feed($food)
    {
        if ($food === $this->foodType) {
            $this->foodReserves += 40;
        } else {
            $this->happiness -= 20;
            $this->foodReserves -= 20;
        }
    }

    public function pet()
    {
        $this->happiness += 20;
    }
}

class Zookeeper
{
    private $animals;

    public function __construct()
    {
        $this->animals = [];
    }

    public function addAnimal(Animal $animal)
    {
        $this->animals[] = $animal;
    }

    public function listAnimals()
    {
        foreach ($this->animals as $index => $animal) {
            echo ($index + 1) . ". " . $animal->getName() . " (Happiness: " . $animal->getHappiness() . ", Food Reserves: " . $animal->getFoodReserves() . ")\n";
        }
    }

    public function selectAnimal($index)
    {
        if (isset($this->animals[$index])) {
            return $this->animals[$index];
        } else {
            return null;
        }
    }
}

$zookeeper = new Zookeeper();
$zookeeper->addAnimal(new Animal('Wolf', 'Meat'));
$zookeeper->addAnimal(new Animal('Elephant', 'Vegetables'));
$zookeeper->addAnimal(new Animal('Horse', 'Fruits'));

while (true) {
    echo "Animals:\n";
    $zookeeper->listAnimals();

    $choice = readline("Choose animal. Enter a number: ");
    if (v::intVal()->validate($choice)) {
        $choice = (int)$choice;
        if ($choice === 0) {
            break;
        }

        $animal = $zookeeper->selectAnimal($choice - 1);
        if ($animal === null) {
            echo "ERROR! Invalid input!\n";
            continue;
        }

        echo "1. Play\n";
        echo "2. Work\n";
        echo "3. Feed\n";
        echo "4. Pet\n";
        $action = readline("Choose action: ");

        if (v::intVal()->between(1, 4)->validate($action)) {
            $action = (int)$action;
            switch ($action) {
                case 1:
                    $animal->play();
                    break;
                case 2:
                    $animal->work();
                    break;
                case 3:
                    $food = readline("Enter the type of food: ");
                    if (v::stringType()->notEmpty()->validate($food)) {
                        $animal->feed($food);
                    } else {
                        echo "Invalid food type.\n";
                        continue 2;
                    }
                    break;
                case 4:
                    $animal->pet();
                    break;
            }
        } else {
            echo "Invalid action.\n";
            continue;
        }

        echo $animal->getName() . " (Happiness: " . $animal->getHappiness() . ", Food Reserves: " . $animal->getFoodReserves() . ")\n";
    } else {
        echo "Invalid input! Please enter a number.\n";
    }
}
