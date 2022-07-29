<?php

namespace OTP\Objects;

interface IMoSessions
{
    static function addSessionVar($key,$val);
    static function getSessionVar($key);
    static function unsetSession($key);
    static function checkSession();
}