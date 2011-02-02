<?php
class Frapi_Response_Custom extends Frapi_Response
{
	/**
     * Set the data
     *
     * This method sets the data variable to be used in the output.
     *
     * @param  string $data The data to set in the response.
     */
    public function setData($data)
    {
        $this->data = $data;
    }
}