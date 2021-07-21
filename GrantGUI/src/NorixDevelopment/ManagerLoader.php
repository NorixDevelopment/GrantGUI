<?php


namespace NorixDevelopment;

use Exception;
use NorixDevelopment\commands\CommandManager;

trait ManagerLoader // Not really necessary but added in-case.
{
    public function initManagers(): bool
    {
        $managers = [
            CommandManager::class,
        ];

        foreach ($managers as $manager) {

            $className = basename($manager);
            $managerName = substr($className, 0, strpos($className, "Manager"));
            $property = explode("\\", strtolower($managerName) . "Manager");
            try {

                $this->setManager(end($property), new $manager($this));

            } catch (Exception $exception) {
                $this->getLogger()->error("Error found whilst loading " . $className . ".");
                $this->getLogger()->error("Line: " . $exception->getLine());
                $this->getLogger()->error("Message: " . $exception->getMessage());
                return false;
            }
        }
        return true;
    }

}