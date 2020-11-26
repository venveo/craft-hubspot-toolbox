<?php
namespace venveo\hubspottoolbox\entities;

use craft\base\Model;

abstract class HubSpotEntity extends Model {
    /**
     * @param bool $filterNull
     * @return array
     */
    public function prepareForApi($filterNull = true): array
    {
        $data = [];
        foreach($this->getAttributes() as $attribute => $value) {
            if ($value instanceof self) {
                $data[$attribute] = $this->$attribute->prepareForApi();
            } elseif (is_array($value)) {
                $data[$attribute] = array_map(function($val) {
                    if ($val instanceof self) {
                        return $val->prepareForApi();
                    }
                    return $val;
                }, $value);
            } else {
                $data[$attribute] = $value;
            }
        }
        if ($filterNull) {
            $data = array_filter($data);
        }
        return $data;
    }
}