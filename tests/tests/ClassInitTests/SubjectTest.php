<?php
/*
    SubjectTest.php

    Copyright (c) 2014-2021 Snowplow Analytics Ltd. All rights reserved.

    This program is licensed to you under the Apache License Version 2.0,
    and you may not use this file except in compliance with the Apache License
    Version 2.0. You may obtain a copy of the Apache License Version 2.0 at
    http://www.apache.org/licenses/LICENSE-2.0.

    Unless required by applicable law or agreed to in writing,
    software distributed under the Apache License Version 2.0 is distributed on
    an 'AS IS' BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either
    express or implied. See the Apache License Version 2.0 for the specific
    language governing permissions and limitations there under.

    Authors: Joshua Beemster
    Copyright: Copyright (c) 2014-2021 Snowplow Analytics Ltd
    License: Apache License Version 2.0
*/

use Snowplow\Tracker\Subject;
use PHPUnit\Framework\TestCase;

/**
 * Tests all of the functions for adding information
 * into the Tracker Subject.
 */
class SubjectTest extends TestCase
{
    /** @var Subject */
    private $subject;

    // Helper Functions

    protected function setUp(): void
    {
        $this->subject = new Subject();
    }

    private function getTrackerSettings(): array
    {
        return $this->subject->returnTrackerSettings();
    }

    // Tests

    public function testSubjectCreation(): void
    {
        $subject = new Subject();
        $this->assertEquals($this->subject, $subject);
    }

    public function testGetSubject(): void
    {
        $array = $this->subject->getSubject();
        $this->assertEquals(['p' => 'srv'], $array);
    }

    public function testAddPlatform(): void
    {
        $this->subject->setPlatform('platform');
        $settings = $this->getTrackerSettings();

        $this->assertArrayHasKey('p', $settings);
        $this->assertEquals('platform', $settings['p']);
    }

    public function testAddUserId(): void
    {
        $this->subject->setUserID('user_id_1');
        $settings = $this->getTrackerSettings();
        $this->assertArrayHasKey('uid', $settings);
        $this->assertEquals('user_id_1', $settings['uid']);
    }

    public function testAddScreenRes(): void
    {
        $this->subject->setScreenResolution(1024, 768);
        $settings = $this->getTrackerSettings();
        $this->assertArrayHasKey('res', $settings);
        $this->assertEquals('1024x768', $settings['res']);
    }

    public function testAddViewPort(): void
    {
        $this->subject->setViewPort(1366, 768);
        $settings = $this->getTrackerSettings();
        $this->assertArrayHasKey('vp', $settings);
        $this->assertEquals('1366x768', $settings['vp']);
    }

    public function testAddColorDepth(): void
    {
        $this->subject->setColorDepth(100000);
        $settings = $this->getTrackerSettings();
        $this->assertArrayHasKey('cd', $settings);
        $this->assertEquals(100000, $settings['cd']);
    }

    public function testAddTimezone(): void
    {
        $this->subject->setTimezone('timezone');
        $settings = $this->getTrackerSettings();
        $this->assertArrayHasKey('tz', $settings);
        $this->assertEquals('timezone', $settings['tz']);
    }

    public function testAddLanguage(): void
    {
        $this->subject->setLanguage('eng');
        $settings = $this->getTrackerSettings();
        $this->assertArrayHasKey('lang', $settings);
        $this->assertEquals('eng', $settings['lang']);
    }

    public function testAddIpAddress(): void
    {
        $this->subject->setIpAddress('127.0.0.1');
        $settings = $this->getTrackerSettings();
        $this->assertArrayHasKey('ip', $settings);
        $this->assertEquals('127.0.0.1', $settings['ip']);
    }

    public function testAddUseragent(): void
    {
        $this->subject->setUseragent('user_agent');
        $settings = $this->getTrackerSettings();
        $this->assertArrayHasKey('ua', $settings);
        $this->assertEquals('user_agent', $settings['ua']);
    }

    public function testAddDomainUserId(): void
    {
        $this->subject->setDomainUserId('domain_id');
        $settings = $this->getTrackerSettings();
        $this->assertArrayHasKey('duid', $settings);
        $this->assertEquals('domain_id', $settings['duid']);
    }

    public function testAddNetworkUserId(): void
    {
        $this->subject->setNetworkUserId('tnuid');
        $settings = $this->getTrackerSettings();
        $this->assertArrayHasKey('tnuid', $settings);
        $this->assertEquals('tnuid', $settings['tnuid']);
    }
}
