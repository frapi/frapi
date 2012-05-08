<?php

class MockFrapi_Controller_Main extends Frapi_Controller_Main
{
    public function getDefaultFormatFromConfiguration()
    {
        return 'JSON';
    }

}