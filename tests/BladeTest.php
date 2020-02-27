<?php

declare(strict_types=1);

namespace Orchid\Experiment\Tests;

use Orchestra\Testbench\TestCase;

class BladeTest extends TestCase
{
    use Environment;

    /**
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \Throwable
     */
    public function testCookieBlade()
    {
        $value = $this->getExperiment()
            ->startAndSaveCookie($this->experiments);

        $view = view('experiment')->render();

        $this->assertStringContainsString("<span>$value</span>", $view);
    }

    /**
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \Throwable
     */
    public function testNotFoundBlade()
    {
        $this->getExperiment()->start($this->experiments);

        $view = view('experiment')->render();

        $this->assertStringContainsString('<span>Not found</span>', $view);
    }
}
