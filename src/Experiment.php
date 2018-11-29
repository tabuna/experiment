<?php

declare(strict_types=1);

namespace Orchid\Experiment;

use Illuminate\Contracts\Cache\Repository;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Cookie;

class Experiment
{
    /**
     * History Store.
     *
     * @var Repository
     */
    private $store;

    /**
     * The key of the experiment for cookie
     *
     * @var string
     */
    private $key;

    /**
     * Experiment constructor.
     *
     * @param string $key
     * @param Repository $store
     */
    public function __construct(string $key = 'AB', Repository $store = null)
    {
        $this->store = $store ?? Cache::store();
        $this->key   = $key;
    }

    /**
     * @param array $experiments
     *
     * @return string|null
     * @throws \Exception
     */
    public function startAndSaveCookie(array $experiments): ?string
    {
        $ab = $this->start($experiments);

        Cookie::queue(cookie($this->key, $ab));

        return $ab;
    }

    /**
     * Begins the experiment, returning its value.
     *
     * @param array $experiments
     *
     * @return string|null
     * @throws \Exception
     */
    public function start(array $experiments): ?string
    {
        if (empty($experiments)) {
            return null;
        }

        $select = $this->getKeyFromRequest();

        if ($select !== null) {
            return $select;
        }

        $prepareExperiments = $this->prepareExperiments($experiments);

        foreach ($experiments as $key => $maxValue) {

            if ($prepareExperiments[$key] >= $maxValue) {
                continue;
            }

            $this->store->increment($key);

            return $key;
        }

        $this->resetHistory($prepareExperiments);

        return $this->start($experiments);
    }

    /**
     * @return mixed|null
     */
    private function getKeyFromRequest()
    {
        return request()->get($this->key)
            ?? request()->cookie($this->key)
            ?? $_GET[$this->key]
            ?? $_COOKIE[$this->key]
            ?? null;
    }

    /**
     * @param array $experiments
     *
     * @return array
     * @throws \Exception
     */
    private function prepareExperiments(array $experiments = []): array
    {
        foreach ($experiments as $key => $experiment) {
            $experiments[$key] = $this->store->get($key);
        }

        return $experiments;
    }

    /**
     * @param array $keys
     */
    private function resetHistory(array $keys)
    {
        foreach ($keys as $key => $value) {
            $this->store->decrement($key, $value);
        }
    }

}