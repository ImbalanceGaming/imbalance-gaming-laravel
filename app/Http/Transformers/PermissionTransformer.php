<?php

namespace imbalance\Http\Transformers;


class PermissionTransformer extends Transformer {

    public function transform($permission) {

        if (!$permission) {
            return null;
        }

        return [
            'id' => (int)$permission['id'],
            'name' => $permission['name'],
            'description' => $permission['description'],
            'view' => $permission['view'],
            'add' => $permission['add'],
            'edit' => $permission['edit'],
            'delete' => $permission['delete'],
        ];

    }

}