<?php

namespace FFMpeg\Tests\Filters\Video;

use FFMpeg\Filters\Video\ConcatFilter;
use FFMpeg\Filters\Video\FrameRateFilter;
use FFMpeg\Tests\TestCase;
use FFMpeg\Coordinate\FrameRate;

class ConcatFilterTest extends TestCase
{
    public function testApplyConcatFilter()
    {
        $video1 = $this->getVideoMock();
        $video2 = $this->getVideoMock();
        $video3 = $this->getVideoMock();
        $format = $this->getMock('FFMpeg\Format\VideoInterface');

        // create filter and add a video
        $filter = new ConcatFilter();
        $filter->addVideo($video2);
        $filterstr = $filter->apply($video1, $format);

        // check for one concatenated video
        $hasconcat = $hasfile = $hasmapv = $hasmapa = false;
        foreach($filterstr as $f){
            if(preg_match('/-filter_complex "\[0:v:0\] \[0:a:0\] \[1:v:0\] \[1:a:0\]'
                            . ' concat=n=2:v=1:a=1 \[v\] \[a\]"/', $f))
                $hasconcat = true;
            if(preg_match('/-i\b/' . $video2->getPathfile(), $f))
                $hasfile = true;
            if(preg_match('/-map "\[v\]"/', $f))
                $hasmapv = true;
            if(preg_match('/-map "\[a\]"/', $f))
                $hasmapa = true;
        }
        $this->assertTrue($hasconcat, 'concat n=2: -filter_complex incorrect or missing');
        $this->assertTrue($hasfile, 'concat n=2: file incorrect or missing');
        $this->assertTrue($hasmapv, 'concat n=2: video mapping incorrect or missing');
        $this->assertTrue($hasmapa, 'concat n=2: audio mapping incorrect or missing');

        // add second video
        $filter->addVideo($video3);
        $filterstr = $filter->apply($video1, $format);

        // check for two concatenated videos
        $hasconcat = $hasfile = $hasmapv = $hasmapa = false;
        foreach($filterstr as $f){
            if(preg_match('/-filter_complex "\[0:v:0\] \[0:a:0\] \[1:v:0\] \[1:a:0\] \[2:v:0\] \[2:a:0\]'
                            . ' concat=n=3:v=1:a=1 \[v\] \[a\]"/', $f))
                $hasconcat = true;
            if(preg_match('/-i\b/' . $video2->getPathfile(), $f))
                $hasfile = true;
            if(preg_match('/-map "\[v\]"/', $f))
                $hasmapv = true;
            if(preg_match('/-map "\[a\]"/', $f))
                $hasmapa = true;
        }
        $this->assertTrue($hasconcat, 'concat n=3: -filter_complex incorrect or missing');
        $this->assertTrue($hasfile, 'concat n=3: file incorrect or missing');
        $this->assertTrue($hasmapv, 'concat n=3: video mapping incorrect or missing');
        $this->assertTrue($hasmapa, 'concat n=3: audio mapping incorrect or missing');
    }
}
