<?php

class MockFrapi_Controller_Api extends Frapi_Controller_Api
{
    public function parseMimeTypes() {
        $patterns = array(
            array(
                'mimetype' => 'application/vnd.test.:format',
                'output_format' => ':format',
                'hash' => 'adea02c1d8a51bdfbd41b9ee199cd3b19c0f249e',
                'pattern' => '@^application/vnd\\.test\\.(?P<format>[^\.\+]*?)$@',
                'params' =>
                array(
                    'format',
                ),
            ),
            array(
                'mimetype' => 'application/vnd.test.:version.:format',
                'output_format' => ':format',
                'hash' => 'adea02c1d8a51bdfbd41b9ee199c19c0f249e',
                'pattern' => '@^application/vnd\\.test\\.(?P<version>[^\.\+]*?)\\.(?P<format>[^\.\+]*?)$@',
                'params' =>
                array(
                    'version',
                    'format',
                ),
            ),
            array(
                'mimetype' => 'application/vnd.test.:version+json',
                'output_format' => 'json',
                'hash' => 'adea02c1d8a51bdfbd41b9eec19c0f249e',
                'pattern' => '@^application/vnd\\.test\\.(?P<version>[^\.\+]*?)\\+json$@',
                'params' =>
                array(
                    'version',
                ),
            ),
        );

        return $patterns;
    }
}