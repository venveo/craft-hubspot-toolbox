<?php
/**
 * @link https://craftcms.com/
 * @copyright Copyright (c) Pixel & Tonic, Inc.
 * @license MIT
 */

namespace venveo\hubspottoolbox\events;

use yii\base\Event;

class FormSubmittedEvent extends Event
{
    public $submission;
}
