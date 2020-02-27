<?php

declare(strict_types=1);

namespace Orchid\Experiment\Tests;

use Illuminate\Contracts\Cache\Repository;
use Illuminate\Support\Facades\Cache;
use Orchid\Experiment\Experiment;
use Orchid\Experiment\ExperimentServiceProvider;

/**
 * Trait Environment.
 */
trait Environment
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

    protected function getEnvironmentSetUp()
    {
        config()->set('view.paths', [
            __DIR__.'/stubs/',
        ]);
    }

    /**
     * @return array
     */
    protected function getPackageProviders()
    {
        return [
            ExperimentServiceProvider::class,
        ];
    }

    /**
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->store = Cache::store();

        foreach ($this->experiments as $key => $value) {
            $this->store->set($key, 0);
        }

        unset($_COOKIE[$this->key], $_GET[$this->key]);
    }

    /**
     * @throws \Exception
     *
     * @return \Orchid\Experiment\Experiment
     */
    public function getExperiment(): Experiment
    {
        return new Experiment($this->key);
    }
}
