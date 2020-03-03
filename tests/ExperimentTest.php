<?php

declare(strict_types=1);

namespace Orchid\Experiment\Tests;

class ExperimentTest extends TestCase
{
    /**
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function testForNewValue(): void
    {
        $value = $this->getExperiment()
            ->start($this->experiments);

        $this->assertNotNull($value);
    }

    /**
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function testSetCookieValue(): void
    {
        $value = $this->getExperiment()
            ->startAndSaveCookie($this->experiments);

        $this->assertNotNull($value);
    }

    /**
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function testReadCookieValue(): void
    {
        $items = array_keys($this->experiments);
        $rand = $items[array_rand($items)];

        $_COOKIE[$this->key] = $rand;

        $value = $this->getExperiment()
            ->start($this->experiments);

        $this->assertEquals($rand, $value);
    }

    /**
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function testReadGetValue(): void
    {
        $items = array_keys($this->experiments);
        $rand = $items[array_rand($items)];

        $_GET[$this->key] = $rand;

        $value = $this->getExperiment()
            ->start($this->experiments);

        $this->assertEquals($rand, $value);
    }

    /**
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function testEmptyArguments(): void
    {
        $value = $this->getExperiment()
            ->start([]);

        $this->assertEquals(null, $value);

        $value = $this->getExperiment()
            ->startAndSaveCookie([]);

        $this->assertNull($value);
    }

    public function testRatioPercentage(): void
    {
        $count = [
            'Orchid_EXPERIMENT_PROJECT_BLOCK_MENU'   => 0,
            'Orchid_EXPERIMENT_PROJECT_HEADER_SUPER' => 0,
        ];

        for ($i = 0; $i < 1000; $i++) {
            $value = $this->getExperiment()
                ->start($this->experiments);

            $count[$value]++;
        }

        self::assertEquals($count, [
            'Orchid_EXPERIMENT_PROJECT_BLOCK_MENU'   => 10,
            'Orchid_EXPERIMENT_PROJECT_HEADER_SUPER' => 990,
        ]);
    }
}
