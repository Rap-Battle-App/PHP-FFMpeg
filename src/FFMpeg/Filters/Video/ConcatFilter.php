<?php

/*
 * This file is part of PHP-FFmpeg.
 *
 * (c) Alchemy <dev.team@alchemy.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FFMpeg\Filters\Video;

use FFMpeg\Format\VideoInterface;
use FFMpeg\Media\Video;

/**
 * This class implements an interface to the concatenation filter
 * as defined here: https://trac.ffmpeg.org/wiki/Concatenate
 *
 * add this filter to a video object and add any other videos to this filter
 */
class ConcatFilter implements VideoFilterInterface
{
    /** @var integer */
    private $priority;

    /** list of additional videos to concatenate */
    private $videos; 

    /**
     * A custom filter, useful if you want to build complex filters
     *
     * @param string $filter
     * @param int    $priority
     */
    public function __construct($priority = 0)
    {
        $this->priority = $priority;

        $this->videos = array();
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * {@inheritdoc}
     */
    public function apply(Video $video, VideoInterface $format)
    {
        $commands = array();
        if(!empty($this->videos)){
            $concatfilter = '[0:v:0] [0:a:0]';

            $i = 1;
            foreach($this->videos as $video){
                // add video file to input
                $commands = array_merge($commands, ['-i', $video->getPathfile()]);
                // define concat filter
                $concatfilter = $concatfilter . ' [' . $i . ':v:0] [' . $i . ':a:0]';
                $i++;
            }

            $concatfilter = $concatfilter . ' concat=n=' . $i . ':v=1:a=1 [v] [a]';
            $commands = array_merge($commands, ['-filter_complex', $concatfilter]);
            $commands = array_merge($commands, ['-map', '[v]']);
            $commands = array_merge($commands, ['-map', '[a]']);
        }
        return $commands;
    }

    public function addVideo(Video $video){
        if(isset($video) && !is_null($video)){
            array_push($this->videos, $video);
        }
    }
}
