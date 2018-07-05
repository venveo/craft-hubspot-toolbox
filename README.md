# HubSpot Toolbox plugin for Craft CMS 3.x

## Installation

        composer require venveo/hubspot-toolbox

3. In the Control Panel, go to Settings → Plugins and click the “Install” button for HubSpot Toolbox.

## HubSpot Toolbox Overview

## Configuring HubSpot Toolbox
1. Copy the default config file to your config folder as `hubspot-toolbox.php`
2. Create an app in your HubSpot developer console with the proper scopes.
3. Add the HUBSPOT_PORTAL_ID env value to your `.env`
4. Configure your app within the hubspot-toolbox.php config file:
    ```php
    <?php
    return  [
        'hubspotPortalId' => getenv('HUBSPOT_PORTAL_ID'),
        'defaultApp' => 'some_unique_id',
    
        'apps' => [
            'some_unique_id' => [
                'appName' => 'a name for my app',
                'appId' => 000000,
                'clientId' => 'probably_put_this_in_your_env',
                'clientSecret' => 'probably_put_this_in_your_env',
                // Update me
                'scopes' => ['timeline', 'contacts', 'forms']
            ]
        ]
    ];
    ```
5. Enable plugin
6. Go to plugin settings, click the login button!

## Using HubSpot Toolbox

-Insert text here-

## HubSpot Toolbox Roadmap

Some things to do, and ideas for potential features:

* Release it

Brought to you by [Venveo](https://venveo.com)
