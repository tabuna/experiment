<?php

declare(strict_types=1);

namespace Orchid\Experiment\Tests;

class BladeTest extends TestCase
{
    /**
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \Throwable
     */
    public function testCookieBlade(): void
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
    public function testNotFoundBlade(): void
    {
        $this->getExperiment()->start($this->experiments);

        $view = view('experiment')->render();

        $this->assertStringContainsString('<span>Not found</span>', $view);
    }
}
