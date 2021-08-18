<?php
/*
    EmitterTest.php

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

use Snowplow\Tracker\Emitters\SyncEmitter;
use Snowplow\Tracker\Emitters\CurlEmitter;
use Snowplow\Tracker\Emitters\SocketEmitter;
use Snowplow\Tracker\Emitters\FileEmitter;
use PHPUnit\Framework\TestCase;

/**
 * Tests the creation of all the emitters.
 */
class EmitterTest extends TestCase
{

    /**
     * @throws ErrorException
     */
    public function testCurlEmitterInit(): void
    {
        $emitter = new CurlEmitter('collector.acme.au', null, 'GET', 1, false);

        // Asserts
        $this->assertNotNull($emitter);
    }

    public function testSyncEmitterInit(): void
    {
        $emitter = new SyncEmitter('collecter.acme.au', 'http', 'GET', 1, false);

        // Asserts
        $this->assertNotNull($emitter);
    }

    public function testSocketEmitterInit(): void
    {
        $emitter = new SocketEmitter('collecter.acme.au', null, 'GET', null, null, false);

        // Asserts
        $this->assertNotNull($emitter);
    }

    /**
     * @throws ErrorException
     */
    public function testFileEmitterInit(): void
    {
        $emitter = new FileEmitter('collecter.acme.au', null, 'GET', 1, 15, 1);

        // Asserts
        $this->assertNotNull($emitter);
    }

    /**
     * @throws ErrorException
     */
    public function testReturnFunctions(): void
    {
        $emitter = new SyncEmitter('collecter.acme.au', 'http', 'GET', 10, false);
        $emitter->addEvent(['something' => 'something']);
        $payload = [];
        $payload_updated = $emitter->updateStm($payload);

        $this->assertEquals(
            false,
            $emitter->returnDebugMode()
        );
        $this->assertEquals(
            null,
            $emitter->returnDebugFile()
        );
        $this->assertCount(
            1,
            $emitter->returnBuffer()
        );
        $this->assertArrayHasKey('stm', $payload_updated);
        $this->assertIsNumeric($payload_updated['stm']);
    }
}
