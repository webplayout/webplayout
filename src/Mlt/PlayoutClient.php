<?php

namespace App\Mlt;

class PlayoutClient extends Playout
{
    function _connect()
    {
        $expectWelcome = true;// fixes issues with status command

        $this->Mvcp->connect($expectWelcome);
    }
}
