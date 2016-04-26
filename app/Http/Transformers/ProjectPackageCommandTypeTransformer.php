<?php

namespace imbalance\Http\Transformers;

class ProjectPackageCommandTypeTransformer extends Transformer {

    public function transform($projectPackageCommandType) {

        if (!$projectPackageCommandType) {
            return null;
        }

        return [
            'id' => (int)$projectPackageCommandType['id'],
            'name' => $projectPackageCommandType['name'],
        ];

    }

}