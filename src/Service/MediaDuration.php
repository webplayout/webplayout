<?php

declare(strict_types=1);

namespace App\Service;

class MediaDuration
{
    private $ffmpeg_path;

    function __construct($ffmpeg_path)
    {
        $this->ffmpeg_path = $ffmpeg_path;
    }

    function getDuration($file):int
    {
        if ($duration = $this->matchDuration($file)) {
            return $this->durationToSeconds($duration);
        }

        return 0;
    }

    private function matchDuration($file):string
    {
        $command = $this->ffmpeg_path . ' -hide_banner -i ' . escapeshellarg($file) . ' 2>&1';

        $output = shell_exec($command);

        if(preg_match('/\n\s+Duration: (.*?),/', $output, $matches))
        {
            return $matches[1];
        }

        return '';
    }

    /**
     * @return int Centiseconds
     */
    public function durationToSeconds($duration):int
    {
        $time = 0;
        $segments = explode(':', $duration);

        if (sizeof($segments)) {
            $segments = array_reverse($segments);

            array_walk($segments, function(&$value, $key) {
                $value = $value * (60**$key);
                return true;
            });

            $time = array_sum($segments);
        }

        return (int) round($time * 100, 0);
    }
}
