<?php
/*
    SyncEmitter.php

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

namespace Snowplow\Tracker\Emitters;

use Snowplow\Tracker\Emitter;

class SyncEmitter extends Emitter
{
    // Emitter Parameters

    private $type;
    private $url;
    private $debug = false;

    /**
     * Creates a Synchronous Emitter
     *
     * @param string $uri
     * @param string|null $protocol
     * @param string|null $type
     * @param int|null $buffer_size
     * @param bool $debug
     */
    public function __construct(
        string $uri,
        ?string $protocol = null,
        string $type = null,
        int $buffer_size = null,
        bool $debug = false
    ) {
        $this->type = $this->getRequestType($type);
        $this->url = $this->getCollectorUrl($this->type, $uri, $protocol);

        // If debug is on create a requests_results array
        if ($debug === true) {
            $this->debug = true;
            $this->requests_results = [];
        }

        $buffer = $buffer_size === null ? self::SYNC_BUFFER : $buffer_size;

        $this->setup('sync', $debug, $buffer);
    }

    /**
     * Sends data with the configured Request type
     *
     * @param array $buffer
     * @return bool|string $res - Return true or an error string
     */
    public function send(array $buffer)
    {
        if (count($buffer) > 0) {
            $res = true;
            $type = $this->type;

            if ($type === 'GET') {
                $res_ = '';

                foreach ($buffer as $payload) {
                    $res = $this->curlRequest($this->updateStm($payload), $type);

                    if (!is_bool($res)) {
                        $res_ .= $res;
                    }
                }

                if ($res_ !== '') {
                    $res = $res_;
                }
            } else {
                if ($type === 'POST') {
                    $data = $this->getPostRequest($this->batchUpdateStm($buffer));
                    $res = $this->curlRequest($data, $type);
                }
            }

            return $res;
        }

        return 'No events to send';
    }

    // Send Functions

    /**
     * Use cURL to send data to the collector
     *
     * @param array $data - Is the array which is going to be sent in the Request
     * @param string $type - The type of the Request
     * @return bool|string - Return true if successful request or an error string
     */
    private function curlRequest(array $data, string $type)
    {
        $res = true;
        $res_ = '';

        // Create a cURL handle, set transfer options and execute
        $ch = curl_init($this->url);

        if ($type === 'POST') {
            $json_data = json_encode($data);
            $header = [
                'Content-Type: ' . self::POST_CONTENT_TYPE,
                'Accept: ' . self::POST_ACCEPT,
                'Content-Length: ' . strlen($json_data),
            ];

            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        } else {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
            curl_setopt($ch, CURLOPT_URL, "{$this->url}?" . http_build_query($data));
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_exec($ch);

        if (!curl_errno($ch)) {
            if ($this->debug) {
                $info = curl_getinfo($ch);
                $this->storeRequestResults($info['http_code'], $data);

                if ((int)$info['http_code'] !== 200) {
                    $res_ .= "Sync {$type} Request Failed: {$info['http_code']}";
                }
            }
        } else {
            if ($this->debug) {
                $this->storeRequestResults(404, $data);
            }

            $res_ .= "Sync {$type} Request Failed: " . curl_error($ch);
        }

        // Close handle
        curl_close($ch);

        // If request failed return error string, else return true
        if ($res_ !== '') {
            $res = $res_;
        }

        return $res;
    }

    /**
     * Disables debug mode for the emitter
     * - If deleteLocal is true it will also empty
     *   the local cache of stored request codes and
     *   the associated payloads.
     * - Will then trigger a function in the base
     *   emitter class to clean out the physical
     *   debug records.
     *
     * @param bool $deleteLocal - Empty results array
     */
    public function turnOffDebug(bool $deleteLocal): void
    {
        $this->debug = false;

        if ($deleteLocal) {
            $this->requests_results = [];
        }

        // Switch Debug off in Base Emitter Class
        $this->debugSwitch($deleteLocal);
    }

    // Return Functions

    /**
     * Returns an array which has been formatted to be ready for a POST Request
     *
     * @param array $buffer
     * @return array - POST Request formatted array
     */
    private function getPostRequest(array $buffer): array
    {
        return [
            'schema' => self::POST_REQ_SCHEMA,
            'data' => $buffer,
        ];
    }

    // Sync Return Functions

    /**
     * Returns the Emitter Collector URL
     *
     * @return string
     */
    public function returnUrl(): string
    {
        return $this->url;
    }

    /**
     * Returns the Emitter HTTP Request Type
     *
     * @return null|string
     */
    public function returnType(): ?string
    {
        return $this->type;
    }

    // Debug Functions

    /**
     * Returns the array of stored results from a request
     *
     * @return array - Dynamic array of stored results
     */
    public function returnRequestResults(): array
    {
        return $this->requests_results;
    }

    /**
     * Stores all of the parameters of the Request Response into a Dynamic Array for use in unit testing.
     *
     * @param int $code - Is the response from a GET or POST request
     * @param array $data - The payload array that is being sent
     */
    private function storeRequestResults(int $code, array $data): void
    {
        $array['code'] = $code;
        $array['data'] = json_encode($data);
        $this->requests_results[] = $array;
    }
}
