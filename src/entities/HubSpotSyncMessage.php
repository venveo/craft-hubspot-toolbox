<?php

namespace venveo\hubspottoolbox\entities;


const ACTION_UPSERT = 'UPSERT';
const ACTION_DELETE = 'DELETE';

class HubSpotSyncMessage extends HubSpotEntity
{
    public $action = ACTION_UPSERT;
    public $changedAt = null;
    public $externalObjectId = null;
    public array $properties = [];
    public array $associations = [];

    protected function defineRules(): array
    {
        $rules = parent::defineRules();
        $rules[] = [['action', 'externalObjectId', 'properties'], 'required'];
        $rules[] = ['action', 'in', 'range' => [ACTION_UPSERT, ACTION_DELETE]];
        return $rules;
    }
}