<?php

namespace imbalance\Http\Transformers;


class ProjectPackageTransformer extends Transformer {

    public function transform($projectPackage) {

        if (!$projectPackage) {
            return null;
        }

        return [
            'id' => (int)$projectPackage['id'],
            'name' => $projectPackage['name'],
            'repository' => $projectPackage['repository'],
            'deploy_branch' => $projectPackage['deploy_branch'],
            'deploy_location' => $projectPackage['deploy_location'],
            'project_id' => (int)$projectPackage['project_id'],
        ];

    }

}