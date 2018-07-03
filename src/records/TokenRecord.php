<?php
/**
 * HubSpot Toolbox plugin for Craft CMS 3.x
 *
 * Turnkey HubSpot integration for CraftCMS
 *
 * @link      https://venveo.com
 * @copyright Copyright (c) 2018 Venveo
 */

namespace venveo\hubspottoolbox\records;

use craft\db\ActiveRecord;
use League\OAuth2\Client\Token\AccessToken;
use venveo\hubspottoolbox\models\HubSpotApp;
use yii\behaviors\AttributeTypecastBehavior;

/**
 * HubSpotFormRecord Record
 *
 * @property  string formName
 * @property  string formId
 * @property int siteId
 * @property string accessToken
 * @property string appHandle
 * @property null|string refreshToken
 * @property int|mixed|string createdBy
 * @property int expires
 * @property mixed id
 * @author    Venveo
 * @package   HubspotToolbox
 * @since     1.0.0
 */
class TokenRecord extends ActiveRecord
{
    /**
     * @return string
     */
    public static function tableName()
    {
        return '{{%hubspottoolbox_accesstoken}}';
    }

    /**
     * Creates a new instance of TokenRecord from an AccessToken
     *
     * @param AccessToken $token
     * @param HubSpotApp $app
     * @return TokenRecord
     */
    public static function hydrateFromAccessToken(AccessToken $token, HubSpotApp $app): TokenRecord
    {

        $record = new self();
        $record->accessToken = $token->getToken();
        $record->refreshToken = $token->getRefreshToken();
        $record->expires = $token->getExpires();
        $record->appHandle = $app->handle;
        $record->siteId = \Craft::$app->getSites()->getCurrentSite()->id;
        $record->createdBy = \Craft::$app->user->id;
        return $record;
    }
}
