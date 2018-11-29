<?php

declare(strict_types=1);

namespace Orchid\Experiment\Tests;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Cookie;
use Orchestra\Testbench\TestCase;
use Orchid\Experiment\Experiment;
use Illuminate\Contracts\Cache\Repository;

class ExperimentTest extends TestCase
{

    /**
     * @var string
     */
    public $key = 'TEST_EXPERIMENT';

    /**
     * @var array
     */
    public $experiments = [
        'Orchid_EXPERIMENT_PROJECT_BLOCK_MENU'   => 1,
        'Orchid_EXPERIMENT_PROJECT_HEADER_SUPER' => 99,
    ];

    /**
     * @var Repository
     */
    protected $store;


    /**
     * @throws \Exception
     */
    public function setUp()
    {
        parent::setUp();

        $this->store = Cache::store();

        //ob_start();
        foreach ($this->experiments as $key => $value) {
            $this->store->set($key,0);
        }

        unset($_COOKIE[$this->key]);
        unset($_GET[$this->key]);
    }


    /**
     * @throws \Exception
     */
    public function testForNewValue()
    {
        $value = $this->getExperiment()
            ->start($this->experiments);

        $this->assertNotNull($value);
    }

    /**
     * @return \Orchid\Experiment\Experiment
     * @throws \Exception
     */
    public function getExperiment(): Experiment
    {
        return new Experiment($this->key);
    }

    /**
     * @throws \Exception
     */
    public function testSetCookieValue()
    {
        $value = $this->getExperiment()
            ->startAndSaveCookie($this->experiments);

        $this->assertNotNull($value);
    }

    /**
     * @throws \Exception
     */
    public function testReadCookieValue()
    {
        $items = array_keys($this->experiments);
        $rand  = $items[array_rand($items)];

        $_COOKIE[$this->key] = $rand;

        $value = $this->getExperiment()
            ->start($this->experiments);

        $this->assertEquals($rand, $value);
    }

    /**
     * @throws \Exception
     */
    public function testReadGetValue()
    {
        $items = array_keys($this->experiments);
        $rand  = $items[array_rand($items)];

        $_GET[$this->key] = $rand;

        $value = $this->getExperiment()
            ->start($this->experiments);

        $this->assertEquals($rand, $value);
    }

    /**
     * @throws \Exception
     */
    public function testEmptyArguments()
    {
        $value = $this->getExperiment()
            ->start([]);

        $this->assertEquals(null, $value);
    }

    public function testRatioPercentage()
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