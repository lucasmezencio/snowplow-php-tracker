<?php

/*
    Subject.php

    Copyright (c) 2014-2021 Snowplow Analytics Ltd. All rights reserved.

    This program is licensed to you under the Apache License Version 2.0,
    and you may not use this file except in compliance with the Apache License
    Version 2.0. You may obtain a copy of the Apache License Version 2.0 at
    http://www.apache.org/licenses/LICENSE-2.0.

    Unless required by applicable law or agreed to in writing,
    software distributed under the Apache License Version 2.0 is distributed on
    an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either
    express or implied. See the Apache License Version 2.0 for the specific
    language governing permissions and limitations there under.

    Authors: Joshua Beemster
    Copyright: Copyright (c) 2014-2021 Snowplow Analytics Ltd
    License: Apache License Version 2.0
*/

namespace Snowplow\Tracker;

class Subject extends Constants
{

    // Subject Parameters

    private $tracker_settings;

    /**
     * Constructs an array in which subject parameters are stored
     */
    public function __construct()
    {
        $this->tracker_settings = ['p' => self::DEFAULT_PLATFORM];
    }

    /**
     * Returns the subject parameters as an array which can be added to the payload
     *
     * @return array
     */
    public function getSubject(): array
    {
        return $this->tracker_settings;
    }

    // Setter Functions

    /**
     * Sets the platform from which the event is fired
     *
     * @param string $platform
     */
    public function setPlatform(string $platform): void
    {
        $this->tracker_settings['p'] = $platform;
    }

    /**
     * Sets a custom user identification for the event
     *
     * @param string $userId
     */
    public function setUserId(string $userId): void
    {
        $this->tracker_settings['uid'] = $userId;
    }

    /**
     * Sets the screen resolution
     *
     * @param int $width
     * @param int $height
     */
    public function setScreenResolution(int $width, int $height): void
    {
        $this->tracker_settings['res'] = "{$width}x{$height}";
    }

    /**
     * Sets the view port resolution
     *
     * @param int $width
     * @param int $height
     */
    public function setViewPort(int $width, int $height): void
    {
        $this->tracker_settings['vp'] = "{$width}x{$height}";
    }

    /**
     * Sets the colour depth
     *
     * @param int $depth
     */
    public function setColorDepth(int $depth): void
    {
        $this->tracker_settings['cd'] = $depth;
    }

    /**
     * Sets the event timezone
     *
     * @param string $timezone
     */
    public function setTimezone(string $timezone): void
    {
        $this->tracker_settings['tz'] = $timezone;
    }

    /**
     * Sets the language used
     *
     * @param string $language
     */
    public function setLanguage(string $language): void
    {
        $this->tracker_settings['lang'] = $language;
    }

    /**
     * Sets the client's IP Address
     *
     * @param string $ipAddress
     */
    public function setIpAddress(string $ipAddress): void
    {
        $this->tracker_settings['ip'] = $ipAddress;
    }

    /**
     * Sets the Useragent
     *
     * @param string $useragent
     */
    public function setUseragent(string $useragent): void
    {
        $this->tracker_settings['ua'] = $useragent;
    }

    /**
     * Sets the Network User ID
     *
     * @param string $networkUserId
     */
    public function setNetworkUserId(string $networkUserId): void
    {
        $this->tracker_settings['tnuid'] = $networkUserId;
    }

    /**
     * Sets the domain User ID
     *
     * @param string $domainUserId
     */
    public function setDomainUserId(string $domainUserId): void
    {
        $this->tracker_settings['duid'] = $domainUserId;
    }

    /**
     * Sets the referer
     *
     * @param string $refr
     */
    public function setRefr(string $refr): void
    {
        $this->tracker_settings['refr'] = $refr;
    }

    /**
     * Sets the page URL
     *
     * @param string $pageUrl
     */
    public function setPageUrl(string $pageUrl): void
    {
        $this->tracker_settings['url'] = $pageUrl;
    }

    // Subject Return Functions

    public function returnTrackerSettings(): array
    {
        return $this->tracker_settings;
    }
}
