<?php
/*
Copyright 2002-2012 MarkLogic Corporation.  All Rights Reserved.

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

     http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.
*/
namespace MarkLogic\MLPHP;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

/**
 * @package MLPHP
 * @author Eric Bloch <eric.bloch@gmail.com>
 */
class SetupTest extends \PHPUnit_Framework_TestCase
{
    private $apiclient;
    private $client;

    private $db;
    private $host;
    private $port;
    private $user;
    private $pass;

    private $logger;

    function createAPI() 
    {
        $method = 'post';
        $resource = "rest-apis";
        $params = array();
        $headers = array(
            'Content-type' => 'application/json'
        );


        $body = '
            {
                "rest-api": {
                    "name": "test-mlphp-rest-api",
                    "database": "'.$this->db.'",
                    "modules-database": "'.$this->db.'-modules",
                    "port": "'.$this->port.'"
                }
            }
        ';

        $request = new RESTRequest($method, $resource, $params, $body, $headers);

        $this->apiclient->post($request);

        $this->client = new RESTClient($this->host, $this->port, '', 'v1', $this->user, $this->pass, 'digest', $this->logger);

        $this->installExtensions();
    }

    function installExtensions() 
    {
        $this->logger->debug("installExtensions");
        $method = 'put';
        $resource = "config/resources/clear-db";
        $params = array(
            'method' => 'post'
        );
        $headers = array(
            'Content-type' => 'application/xquery'
        );
        $body = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . "clear-db.xqy");
        $request = new RESTRequest($method, $resource, $params, $body, $headers);
        $this->client->put($request);
    }

    function clearDB() 
    {
        $this->logger->debug("clearDB");
        $method = 'post';
        $resource = "resources/clear-db";
        $params = array();
        $headers = array();
        $body = null;

        $request = new RESTRequest($method, $resource, $params, $body, $headers);
        $this->client->post($request);
    }

    function deleteAPI()
    {
        $method = 'delete';
        $resource = "rest-apis/test-mlphp-rest-api";
        $params = array();
        $body = null;
        $headers = array();
        $request = new RESTRequest($method, $resource, $params, $body, $headers);

        $this->apiclient->delete($request);
    }

    function setUp()
    {
        global $mlphp;

        $this->host = $mlphp['host'] ? $mlphp['host'] : 'localhost';
        $this->port = $mlphp['port'] ? $mlphp['port'] : '8234';
        $this->db =   $mlphp['db']   ? $mlphp['db']   : 'mlphp-test';
        $this->user = $mlphp['user'] ? $mlphp['user'] : 'admin';
        $this->pass = $mlphp['pass'] ? $mlphp['pass'] : 'adm1n';
        $this->mgmt_port = $mlphp['mgmt_port'] ? $mlphp['mgmt_port'] : '8002';

        $log_level = $mlphp['log_level'] ? $mlphp['log_level'] : Logger::INFO;

        $this->logger = new Logger('test');
        $this->logger->pushHandler(new StreamHandler('php://stderr', $log_level));

        $this->logger->debug("setUp");

        $this->apiclient = new RESTClient($this->host, $this->mgmt_port, '', 'v1', $this->user, $this->pass, 'digest', $this->logger);

        /* Create a fresh REST API instance for us */
        $this->createAPI();

        /* Clear the attached DB */
        $this->clearDB();
        
    }

    function test404()
    {
        $doc = new Document($this->client, "/not-there");
        $this->assertFalse($doc->read());
    }

    function tearDown()
    {
        $this->logger->debug("tearDown");

        $this->deleteAPI();
    }
}

