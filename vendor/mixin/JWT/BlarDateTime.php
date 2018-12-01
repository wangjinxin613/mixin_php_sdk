<?php
class BlarDateTime extends DateTime
{

    /**
     * Return Date in ISO8601 format
     *
     * @return String
     */
    public function getISO8601Time()
    {
        return $this->format('Y-m-d\TH:i:sO');
    }

}