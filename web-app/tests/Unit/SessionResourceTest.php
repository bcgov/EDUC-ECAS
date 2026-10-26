<?php

namespace Tests\Unit;

use App\Dynamics\Session;
use App\Http\Resources\SessionResource;
use Carbon\Carbon;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SessionResourceTest extends TestCase
{

    public function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow('2026-05-10 12:00:00');
    }

    public function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }


    #[Test]
    public function a_session_that_ended_less_than_a_day_ago_is_not_past()
    {
        $session = $this->sessionEnding('2026-05-10T00:00:00Z');

        $this->assertSame(0, $session['diff_in_days']);
        $this->assertFalse($session['isPast']);
    }

    #[Test]
    public function days_until_a_session_ends_are_whole_days()
    {
        $session = $this->sessionEnding('2026-05-14T00:00:00Z');

        $this->assertSame(3, $session['diff_in_days']);
        $this->assertFalse($session['isPast']);
    }

    #[Test]
    public function a_session_that_ended_days_ago_is_past()
    {
        $session = $this->sessionEnding('2026-05-07T00:00:00Z');

        $this->assertSame(-3, $session['diff_in_days']);
        $this->assertTrue($session['isPast']);
    }


    private function sessionEnding($end_date)
    {
        $resource = new SessionResource([
            'id'            => 'session-1',
            'activity'      => null,
            'type'          => null,
            'start_date'    => '2026-05-01T00:00:00Z',
            'end_date'      => $end_date,
            'location'      => null,
            'address'       => null,
            'city'          => null,
            'is_published'  => true,
            'status'        => Session::$status['Open'],
            'assignment'    => null,
        ]);

        return $resource->toArray(request());
    }

}
