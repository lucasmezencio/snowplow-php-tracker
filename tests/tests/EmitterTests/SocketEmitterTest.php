<?php
/*
    SocketEmitterTest.php

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
use Snowplow\Tracker\Subject;
use Snowplow\Tracker\Emitters\SocketEmitter;
use PHPUnit\Framework\TestCase;

/**
 * Tests the functionality of the Socket emitter
 */
class SocketEmitterTest extends TestCase
{
    // Helper Functions & Values

    private $uri = 'localhost:4545';

    private function requestResultAssert($emitters): void
    {
        foreach ($emitters as $emitter) {
            $results = $emitter->returnRequestResults();

            foreach ($results as $result) {
                $this->assertEquals(200, $result['code']);
            }
        }
    }

    private function returnTracker($type, $debug, $uri): Tracker
    {
        $subject = new Subject();
        $e1 = $this->returnSocketEmitter($type, $uri, $debug);
        return new Tracker($e1, $subject, null, null, true);
    }

    private function returnSocketEmitter($type, $uri, $debug): SocketEmitter
    {
        return new SocketEmitter($uri, null, $type, null, null, $debug);
    }

    // Tests

    /**
     * @throws ErrorException
     */
    public function testSocketDebugGet(): void
    {
        $tracker = $this->returnTracker('GET', true, $this->uri);
        $tracker->flushEmitters();

        for ($i = 0; $i < 1; $i++) {
            $tracker->trackPageView('www.example.com', 'example', 'www.referrer.com');
        }

        $tracker->flushEmitters();

        //Asserts
        $this->requestResultAssert($tracker->returnEmitters());
        $tracker->turnOffDebug(true);
    }

    /**
     * @throws ErrorException
     */
    public function testSocketDebugPost(): void
    {
        $tracker = $this->returnTracker('POST', true, $this->uri);
        $tracker->flushEmitters();

        for ($i = 0; $i < 1; $i++) {
            $tracker->trackPageView('www.example.com', 'example', 'www.referrer.com');
        }

        $tracker->flushEmitters();

        //Asserts
        $this->requestResultAssert($tracker->returnEmitters());
        $tracker->turnOffDebug(true);
    }

    public function testReturnFunctions(): void
    {
        $tracker = $this->returnTracker('GET', false, $this->uri);
        $emitters = $tracker->returnEmitters();
        $emitter = $emitters[0];

        // Asserts
        $this->assertEquals(
            false,
            $emitter->returnSsl()
        );
        $this->assertEquals(
            $this->uri,
            $emitter->returnUri()
        );
        $this->assertEquals(
            30,
            $emitter->returnTimeout()
        );
        $this->assertEquals(
            'GET',
            $emitter->returnType()
        );
        $this->assertEquals(
            null,
            $emitter->returnSocket()
        );
        $this->assertEquals(
            false,
            $emitter->returnSocketIsFailed()
        );
    }
}
