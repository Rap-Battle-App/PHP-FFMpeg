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
        $this->assertEquals(['-i', $video2->getPathFile(),
                            '-filter_complex',
                            '[0:v:0] [0:a:0] [1:v:0] [1:a:0] concat=n=2:v=1:a=1 [v] [a]',
                            '-map', '[v]',
                            '-map', '[a]'],
                            $filterstr);

        $filter->addVideo($video3);
        $filterstr = $filter->apply($video1, $format);

        // check for two concatenated videos
        $this->assertEquals(['-i', $video2->getPathFile(),
                            '-i', $video3->getPathFile(),
                            '-filter_complex',
                            '[0:v:0] [0:a:0] [1:v:0] [1:a:0] [2:v:0] [2:a:0] concat=n=3:v=1:a=1 [v] [a]',
                            '-map', '[v]',
                            '-map', '[a]'],
                            $filterstr);
    }
}
