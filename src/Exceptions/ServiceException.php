<?php

namespace Arkade\RetailDirections\Exceptions;

use Illuminate\Support\Collection;

class ServiceException extends \Exception
{
    /**
     * History container for exception.
     *
     * @var Collection|null
     */
    protected $historyContainer;

    /**
     * Get history container attached to this exception.
     *
     * @return Collection|null
     */
    public function getHistoryContainer()
    {
        return $this->historyContainer;
    }

    /**
     * Set history container attached to this exception.
     *
     * @param  Collection|null $historyContainer
     * @return ServiceException
     */
    public function setHistoryContainer(Collection $historyContainer = null)
    {
        $this->historyContainer = $historyContainer;

        return $this;
    }
}