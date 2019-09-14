<?php

/**
 * This code was generated by
 * \ / _    _  _|   _  _
 * | (_)\/(_)(_|\/| |(/_  v1.0.0
 * /       /
 */

namespace Twilio\Rest\FlexApi\V1;

use Twilio\Options;
use Twilio\Values;

abstract class WebChannelOptions {
    /**
     * @param string $chatUniqueName Chat channel unique name
     * @param string $preEngagementData Pre-engagement data
     * @return CreateWebChannelOptions Options builder
     */
    public static function create($chatUniqueName = Values::NONE, $preEngagementData = Values::NONE) {
        return new CreateWebChannelOptions($chatUniqueName, $preEngagementData);
    }

    /**
     * @param string $chatStatus Chat status
     * @param string $postEngagementData Post-engagement data
     * @return UpdateWebChannelOptions Options builder
     */
    public static function update($chatStatus = Values::NONE, $postEngagementData = Values::NONE) {
        return new UpdateWebChannelOptions($chatStatus, $postEngagementData);
    }
}

class CreateWebChannelOptions extends Options {
    /**
     * @param string $chatUniqueName Chat channel unique name
     * @param string $preEngagementData Pre-engagement data
     */
    public function __construct($chatUniqueName = Values::NONE, $preEngagementData = Values::NONE) {
        $this->options['chatUniqueName'] = $chatUniqueName;
        $this->options['preEngagementData'] = $preEngagementData;
    }

    /**
     * Chat channel unique name
     *
     * @param string $chatUniqueName Chat channel unique name
     * @return $this Fluent Builder
     */
    public function setChatUniqueName($chatUniqueName) {
        $this->options['chatUniqueName'] = $chatUniqueName;
        return $this;
    }

    /**
     * Pre-engagement data
     *
     * @param string $preEngagementData Pre-engagement data
     * @return $this Fluent Builder
     */
    public function setPreEngagementData($preEngagementData) {
        $this->options['preEngagementData'] = $preEngagementData;
        return $this;
    }

    /**
     * Provide a friendly representation
     *
     * @return string Machine friendly representation
     */
    public function __toString() {
        $options = array();
        foreach ($this->options as $key => $value) {
            if ($value != Values::NONE) {
                $options[] = "$key=$value";
            }
        }
        return '[Twilio.FlexApi.V1.CreateWebChannelOptions ' . implode(' ', $options) . ']';
    }
}

class UpdateWebChannelOptions extends Options {
    /**
     * @param string $chatStatus Chat status
     * @param string $postEngagementData Post-engagement data
     */
    public function __construct($chatStatus = Values::NONE, $postEngagementData = Values::NONE) {
        $this->options['chatStatus'] = $chatStatus;
        $this->options['postEngagementData'] = $postEngagementData;
    }

    /**
     * Chat status, can only used to make chat 'inactive'
     *
     * @param string $chatStatus Chat status
     * @return $this Fluent Builder
     */
    public function setChatStatus($chatStatus) {
        $this->options['chatStatus'] = $chatStatus;
        return $this;
    }

    /**
     * Post-engagement data
     *
     * @param string $postEngagementData Post-engagement data
     * @return $this Fluent Builder
     */
    public function setPostEngagementData($postEngagementData) {
        $this->options['postEngagementData'] = $postEngagementData;
        return $this;
    }

    /**
     * Provide a friendly representation
     *
     * @return string Machine friendly representation
     */
    public function __toString() {
        $options = array();
        foreach ($this->options as $key => $value) {
            if ($value != Values::NONE) {
                $options[] = "$key=$value";
            }
        }
        return '[Twilio.FlexApi.V1.UpdateWebChannelOptions ' . implode(' ', $options) . ']';
    }
}