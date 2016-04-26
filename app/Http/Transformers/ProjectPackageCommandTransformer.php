<?php

namespace imbalance\Http\Transformers;


use imbalance\Models\ProjectPackageCommand;
use imbalance\Models\ProjectPackageCommandType;

class ProjectPackageCommandTransformer extends Transformer {

    /**
     * @param ProjectPackageCommand $projectPackageCommand
     * @return array|null
     */
    public function transform($projectPackageCommand) {

        if (!$projectPackageCommand) {
            return null;
        }

        /** @var ProjectPackageCommandType $commandType */
        $commandType = ProjectPackageCommandType::find($projectPackageCommand['project_package_command_type_id']);

        return [
            'id' => (int)$projectPackageCommand['id'],
            'command' => $projectPackageCommand['command'],
            'order' => (int)$projectPackageCommand['order'],
            'run_on' => $projectPackageCommand['run_on'],
            'command_type' => $commandType->name,
            'project_package_id' => (int)$projectPackageCommand['project_package_id'],
        ];

    }

}