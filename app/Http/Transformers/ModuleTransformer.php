<?php

namespace imbalance\Http\Transformers;


trait ModuleTransformer {

    use Transformer;

    public function transform($moduleDetails) {

        return [
            'moduleId' => (int)$moduleDetails['id'],
            'key' => $moduleDetails['key'],
            'name' => $moduleDetails['name'],
            'description' => $moduleDetails['description'],
            'module_sections' => [
                'id' => (int)$moduleDetails['module_sections']['id']
            ]
        ];

    }

    public function transformWithRelation($moduleDetails) {

    }

}