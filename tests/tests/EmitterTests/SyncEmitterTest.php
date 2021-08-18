<?php
/*
    SyncEmitterTest.php

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

use Snowplow\Tracker\Tracker;
use Snowplow\Tracker\Emitters\SyncEmitter;
use Snowplow\Tracker\Subject;
use PHPUnit\Framework\TestCase;

/**
 * Tests the functionality of the Synchronous emitter
 */
class SyncEmitterTest extends TestCase
{
    // Helper Functions & Values

    private $uri = 'localhost:4545';

    private function requestResultAssert($emitters, $code): void
    {
        foreach ($emitters as $emitter) {
            $results = $emitter->returnRequestResults();

            foreach ($results as $result) {
                $this->assertEquals($code, $result['code']);
            }
        }
    }

    private function returnTracker($type, $uri): Tracker
    {
        $subject = new Subject();
        $e1 = $this->returnSyncEmitter($type, $uri, true);

        return new Tracker($e1, $subject, null, null, true);
    }

    private function returnSyncEmitter($type, $uri, $debug, $buffer = 10): SyncEmitter
    {
        return new SyncEmitter($uri, 'http', $type, $buffer, $debug);
    }

    // Tests

    /**
     * @throws ErrorException
     */
    public function testSyncPostBadUri(): void
    {
        $tracker = $this->returnTracker('POST', 'collector.acme.au');
        $tracker->flushEmitters();

        for ($i = 0; $i < 1; $i++) {
            $tracker->trackPageView('www.example.com', 'example', 'www.referrer.com');
        }

        $tracker->flushEmitters();

        //Asserts
        $this->requestResultAssert($tracker->returnEmitters(), 404);
        $tracker->turnOffDebug(true);
    }

    /**
     * @throws ErrorException
     */
    public function testSyncGetBadUri(): void
    {
        $tracker = $this->returnTracker('GET', 'collector.acme.au');
        $tracker->flushEmitters();

        for ($i = 0; $i < 1; $i++) {
            $tracker->trackPageView('www.example.com', 'example', 'www.referrer.com');
        }

        $tracker->flushEmitters();

        //Asserts
        $this->requestResultAssert($tracker->returnEmitters(), 404);
        $tracker->turnOffDebug(true);
    }

    /**
     * @throws ErrorException
     */
    public function testSyncPostDebug(): void
    {
        $tracker = $this->returnTracker('POST', $this->uri);
        $tracker->flushEmitters();

        for ($i = 0; $i < 1; $i++) {
            $tracker->trackPageView('www.example.com', 'example', 'www.referrer.com');
        }

        $tracker->flushEmitters();

        //Asserts
        $this->requestResultAssert($tracker->returnEmitters(), 200);
        $tracker->turnOffDebug(true);
    }

    /**
     * @throws ErrorException
     */
    public function testSyncGetDebug(): void
    {
        $tracker = $this->returnTracker('GET', $this->uri);
        $tracker->flushEmitters();

        for ($i = 0; $i < 1; $i++) {
            $tracker->trackPageView('www.example.com', 'example', 'www.referrer.com');
        }

        $tracker->flushEmitters();

        //Asserts
        $this->requestResultAssert($tracker->returnEmitters(), 200);
        $tracker->turnOffDebug(true);
    }

    public function testSyncBadType(): void
    {
        $e1 = $this->returnSyncEmitter('POSTS', $this->uri, false);

        // Asserts
        $this->assertEquals("http://{$this->uri}/com.snowplowanalytics.snowplow/tp2", $e1->returnUrl());
    }

    public function testReturnFunctions(): void
    {
        $e1 = $this->returnSyncEmitter('GET', $this->uri, false);
        $e2 = $this->returnSyncEmitter('POST', $this->uri, false);

        // Asserts
        $this->assertEquals(
            "http://{$this->uri}/i",
            $e1->returnUrl()
        );
        $this->assertEquals(
            'GET',
            $e1->returnType()
        );
        $this->assertEquals(
            "http://{$this->uri}/com.snowplowanalytics.snowplow/tp2",
            $e2->returnUrl()
        );
        $this->assertEquals(
            'POST',
            $e2->returnType()
        );
    }

    public function testSyncInitNoBuffer(): void
    {
        $e1 = $this->returnSyncEmitter('GET', $this->uri, false, null);
        $e2 = $this->returnSyncEmitter('POST', $this->uri, false, null);

        //Asserts
        $this->assertEquals(50, $e1->returnBufferSize());
        $this->assertEquals(50, $e2->returnBufferSize());
    }
}
