<?php

namespace OpenWorld\Data;

use OpenWorld\Collections\ArrayCollection;
use OpenWorld\Data\Interfaces\DataSourceInterface;
use OpenWorld\Data\Interfaces\DataProviderInterface;
use OpenWorld\Collections\Interfaces\CollectionInterface;


class DataSource implements DataSourceInterface {

    /**
     * Providers registered for current DataSource
     *
     * @var CollectionInterface
     */
    private $providers = null;

    /**
     * DataSource constructor.
     *
     * @param DataProviderInterface[] ...$providers
     */
    public function __construct(DataProviderInterface ...$providers)
    {
        $this->providers = new ArrayCollection();

        foreach($providers as $provider) {
            $this->providers->add($provider);
        }
    }

    public function load($uri = '', $condition = '')
    {
        /* @var $provider DataProviderInterface */
        foreach ($this->providers() as $provider) {
            if ($provider->accept($condition)) {
                $provider->load($uri)->asArray();
            }
        }
    }

    /**
     * Get DataSource's providers, registered for data loading.
     *
     * @return CollectionInterface
     */
    public function &providers() : CollectionInterface
    {
        return $this->providers;
    }
}