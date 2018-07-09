<?php

namespace venveo\hubspottoolbox\models;

use SevenShores;
use yii\base\Model;

/**
 * Class Link
 *
 */
class HubSpotFormSubmission extends Model
{
    public $hubspotForm;
    public $data;
    public $pageURL;
    public $hutk;
    public $pageName;
    public $ipAddress;


    /**
     * @return string
     */
    public function getContext() {
        return json_encode(array_filter([
            'hutk' => $this->hutk,
            'ipAddress' => $this->ipAddress,
            'pageUrl' => $this->pageURL,
            'pageName' => $this->pageName
        ]));
    }

    /**
     * @return array
     */
    public function getData() {
        $data =  $this->preprocessFormData($this->data);
        $data['hs_context'] = $this->getContext();
        unset($data['pageTitle'], $data['pageURL']);
        return $data;
    }

    /**
     * @param $data
     * @return array
     */
    private function preprocessFormData($data)
    {
        return array_map(function($datum) {
            if (is_array($datum)) {
                return implode(';', array_keys($datum));
            }
            return $datum;
        }, $data);
    }

    public function rules() {
    }

}
