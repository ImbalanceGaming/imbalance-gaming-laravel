<?php

namespace imbalance\Http\Transformers;


trait Transformer {

    public abstract function transform($item);

    public abstract function transformWithRelation($item);

    /**
     * Transform a collection
     *
     * @param array $items
     * @return array
     */
    public function transformCollection($items) {
        return array_map([$this, 'transform'], $items->toArray());
    }

    public function transformCollectionWithRelation($items) {
        return array_map([$this, 'transformWithRelation'], $items->toArray());
    }

}