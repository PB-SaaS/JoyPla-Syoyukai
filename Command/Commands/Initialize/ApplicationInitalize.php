<?php

namespace Command\Commands;

use Command\Basis\Command;
use Command\Basis\Request\CommandArgv;
use Command\Commands\Interactor\CreateProjectInteractorInputData;
use Command\Commands\Interactor\CreateProjectInteractorInputPortInterface;

class ApplicationInitalize extends Command {

    private string $serialize = "app:init";
    
    public function __construct(CreateProjectInteractorInputPortInterface $createProjectInteractor)
    {
        $this->inputPort = $createProjectInteractor;
    }

    public function getSerialize()
    {
        return $this->serialize;
    }
    
    public function execute(CommandArgv $commandArgv)
    {
//      ApplicationInitalizeInputData();
        $this->line('Welcome Spiral Frame !!!!');
        $projectName = $this->ask("Please specify project name: ");
        $inputData = new CreateProjectInteractorInputData(['projectName' => ucfirst(strtolower($projectName))]);
        $this->inputPort->execute($inputData);
    }
}