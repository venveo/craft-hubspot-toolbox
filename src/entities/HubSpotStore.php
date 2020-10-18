<?php

namespace venveo\hubspottoolbox\entities;


class HubSpotStore extends HubSpotEntity
{
    public $id;

    public $label;

    public $adminUri;

    protected function defineRules(): array
    {
        $rules = parent::defineRules();
        $rules[] = [['id', 'label'], 'required'];
        return $rules;
    }
}