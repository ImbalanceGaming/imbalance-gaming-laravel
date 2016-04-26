<?php

namespace imbalance\Http\Transformers;


class ProjectTransformer extends Transformer {

    public function transform($project) {

        if (!$project) {
            return null;
        }

        return [
            'id' => (int)$project['id'],
            'key' => $project['key'],
            'name' => $project['name'],
            'description' => $project['description'],
            'url' => $project['url'],
        ];

    }

}