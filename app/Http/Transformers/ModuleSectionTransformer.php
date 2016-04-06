<?php

namespace imbalance\Http\Transformers;


class ModuleSectionTransformer extends Transformer {

    public function transform($moduleSection) {

        if (!$moduleSection) {
            return null;
        }

        return [
            'id' => (int)$moduleSection['id'],
            'name' => $moduleSection['name'],
            'description' => $moduleSection['description'],
            'module_id' => $moduleSection['module_id']
        ];

    }

}