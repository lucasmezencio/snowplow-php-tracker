<?php
/*
    Constants.php

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

use ErrorException;

/**
 * Contains all of the constants needed for the PHP Tracker.
 */
class Constants
{
    /**
     * Settings for the PHP Tracker
     * - Version: the current version of the PHP Tracker
     * - Base64: whether or not we will encode events in Base64 before sending
     * - Debug Log Files: whether or not debug stores physical log files
     *                    if set to false all debug messages will appear in the console
     * - Context: the schema path for a custom-context
     * - Unstruct: the schema path for an unstructured event
     * - Screen View: the schema path for a custom screen view event
     * - Post: the schema path for a POST Payload
     * - Platform: the default platform that we assume the tracker is running on
     * - Post Path: the path appended to the collector uri for all POST requests
     * - Post Content Type: the content type we will be appending to the header for all POST Requests
     * - Post Accept: the type of content that will be accepted by the collector
     * - Get Path: the path appended to the collector uri for all GET requests
     * - Protocol: the default protocol to be used for the collector
     * - SSL: the default for whether or not to use SSL Encryption
     * - Type: the default for what type of request the emitter will be making (POST or GET)
     */
    public const TRACKER_VERSION = 'php-0.4.0';
    public const DEFAULT_BASE_64 = true;
    public const DEBUG_LOG_FILES = true;
    public const CONTEXT_SCHEMA = 'iglu:com.snowplowanalytics.snowplow/contexts/jsonschema/1-0-1';
    public const UNSTRUCT_EVENT_SCHEMA = 'iglu:com.snowplowanalytics.snowplow/unstruct_event/jsonschema/1-0-0';
    public const SCREEN_VIEW_SCHEMA = 'iglu:com.snowplowanalytics.snowplow/screen_view/jsonschema/1-0-0';
    public const POST_REQ_SCHEMA = 'iglu:com.snowplowanalytics.snowplow/payload_data/jsonschema/1-0-4';
    public const DEFAULT_PLATFORM = 'srv';
    public const POST_PATH = '/com.snowplowanalytics.snowplow/tp2';
    public const POST_CONTENT_TYPE = 'application/json; charset=utf-8';
    public const POST_ACCEPT = 'application/json';
    public const GET_PATH = '/i';
    public const DEFAULT_PROTOCOL = 'http';
    public const DEFAULT_SSL = false;
    public const DEFAULT_REQ_TYPE = 'POST';

    /**
     * Settings for the Synchronous Emitter
     * - Buffer: the amount of events that will occur before sending begins
     */
    public const SYNC_BUFFER = 50;

    /**
     * Settings for the Socket Emitter
     * - Buffer: the amount of events that will occur before sending begins
     * - Timeout: the time allowed for sending to the socket before we attempt a reconnect
     */
    public const SOCKET_BUFFER = 50;
    public const SOCKET_TIMEOUT = 30;

    /**
     * Settings for the Asynchronous Rolling Curl Emitter
     * - Buffer: the amount of events that will occur before sending begins
     * - Amount: the amount of times we need to reach the buffer limit
     *   before we initiate sending
     * - Window: the amount of concurrent curl requests being made
     */
    public const CURL_BUFFER = 50;
    public const CURL_AMOUNT_POST = 50;
    public const CURL_WINDOW_POST = 10;
    public const CURL_AMOUNT_GET = 250;
    public const CURL_WINDOW_GET = 30;

    /**
     * Settings for the background File Emitter
     * - Count: The amount of workers that are created
     * - Buffer: the amount of events that will occur before sending begins
     * - Timeout: the amount of time the worker will wait before looking for new log files to process
     *   NOTE: This occurs 5 times before the worker kills itself.
     *         If a new file is found after a timeout the counter will reset.
     * - Folder: the name of the folder which will be created in the root
     *   of this project.  Will hold all of the worker folders and the
     *   'failed' log folder.
     * - Buffer: the amount of events which will be stored in a single
     *   curl. For GET this is always 1.
     * - Window: the amount of concurrent curl requests being made.
     *   NOTE: Each worker will be sending the same amount of concurrent
     *         events.  If lots of logs are failing reduce the concurrent
     *         sending limit.
     */
    public const WORKER_COUNT = 2;
    public const WORKER_BUFFER = 250;
    public const WORKER_TIMEOUT = 15;
    public const WORKER_FOLDER = 'temp/';
    public const WORKER_BUFFER_POST = 50;
    public const WORKER_BUFFER_GET = 1;
    public const WORKER_WINDOW_POST = 10;
    public const WORKER_WINDOW_GET = 30;

    /**
     * Custom handler to turn all PHP Warnings into ErrorExceptions
     *
     * @throws ErrorException
     */
    public function warning_handler(): void
    {
        set_error_handler(static function ($errno, $errstr, $errfile, $errline) {
            throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
        }, E_WARNING);
    }
}
