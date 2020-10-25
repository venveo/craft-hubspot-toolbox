<?php
namespace venveo\hubspottoolbox\helpers;

abstract class HubSpotTimeHelper {
    public static function getChangedAtTimeStamp() {
        return strtotime( gmdate( 'Y-m-d H:i:s ', time() ) ) . '000';
    }
}