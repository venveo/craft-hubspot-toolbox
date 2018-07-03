<?php
/**
 * HubSpot Toolbox plugin for Craft CMS 3.x
 *
 * Turnkey HubSpot integration for CraftCMS
 *
 * @link      https://venveo.com
 * @copyright Copyright (c) 2018 Venveo
 */

/**
 * HubSpot Toolbox config.php
 *
 * This file exists only as a template for the HubSpot Toolbox settings.
 * It does nothing on its own.
 *
 * Don't edit this file, instead copy it to 'craft/config' as 'hubspot-toolbox.php'
 * and make your changes there to override default settings.
 *
 * Once copied to 'craft/config', this file will be multi-environment aware as
 * well, so you can have different settings groups for each environment, just as
 * you do for 'general.php'
 */

return [
    'hubspotPortalId' => getenv('HUBSPOT_PORTAL_ID'),
    'defaultApp' => getenv('HUBSPOT_DEFAULT_APP'),

    'apps' => []
];
