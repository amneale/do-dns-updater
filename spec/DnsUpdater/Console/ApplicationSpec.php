<?php

namespace spec\DnsUpdater\Console;

use DnsUpdater\Console\Application;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ApplicationSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(Application::class);
    }

    function it_extends_base_application()
    {
        $this->shouldHaveType(\Symfony\Component\Console\Application::class);
    }
}
