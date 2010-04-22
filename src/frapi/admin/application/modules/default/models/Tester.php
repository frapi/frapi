<?php

class Default_Model_Tester extends Lupin_Model_DB
{
    public function buildForm()
    {
        return '
        <div id="params-container">
            <div id="params">
            </div>
            <em>Add a parameter ("name=value")</em>:<img src="/images/add.png" id="add" />
        </div>';
    }
}