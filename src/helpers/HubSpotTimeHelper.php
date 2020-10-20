<?php
namespace venveo\hubspottoolbox\helpers;

abstract class HubSpotTimeHelper {
    public static function getChangedAtTimeStamp() {
        return (int)(microtime(true) * 1000);
    }
}