<?php

namespace imbalance\Http\Transformers;


class ModuleTransformer extends Transformer {

    public function transform($module) {

        if (!$module) {
            return null;
        }

        return [
            'id' => (int)$module['id'],
            'key' => $module['key'],
            'name' => $module['name'],
            'description' => $module['description']
        ];

    }

}