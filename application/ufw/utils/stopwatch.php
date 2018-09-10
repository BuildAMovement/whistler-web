<?php
namespace ufw\utils;

class stopwatch
{

    protected $startedAt;

    public function __construct()
    {
        $this->reset();
    }

    // private
    protected function getmicrotime()
    {
        list ($usec, $sec) = explode(' ', microtime());
        return ((float) $usec + (float) $sec);
    }

    // public - resets the timer
    public function reset()
    {
        $this->startedAt = $this->getmicrotime();
    }

    // returns time elapsed
    public function elapsed()
    {
        return $this->getmicrotime() - $this->startedAt;
    }

    // human readable elapsed time
    public function elapsed_hr($decimals = 2)
    {
        $seconds = $this->getmicrotime() - $this->startedAt;
        if ($seconds < 60) {
            return sprintf("%05.{$decimals}f", $seconds);
        } elseif ($seconds < 3600) {
            $mins = (int) ($seconds / 60);
            $seconds = $seconds - $mins * 60;
            return sprintf("%02d:%05.{$decimals}f", $mins, $seconds);
        } else {
            $hours = (int) ($seconds / 3600);
            $seconds = $seconds - $hours * 3600;
            $mins = (int) ($seconds / 60);
            $seconds = $seconds - $mins * 60;
            return sprintf("%02d:%02d:%05.{$decimals}f", $hours, $mins, $seconds);
        }
    }
}
