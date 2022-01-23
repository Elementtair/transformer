<?php
/**
 * This file is part of the ForkBB <https://github.com/forkbb>.
 *
 * @copyright (c) Visman <mio.visman@yandex.ru, https://github.com/MioVisman>
 * @license   The MIT License (MIT)
 */

declare(strict_types=1);

namespace ForkBB\Models;

use ForkBB\Core\Container;
use ForkBB\Models\Manager;

class Action
{
    /**
     * Контейнер
     * @var Container
     */
    protected $c;

    /**
     * Модель
     * @var Manager
     */
    protected $manager;

    public function __construct(Container $container)
    {
        $this->c = $container;
    }

    /**
     * Объявление менеджера
     */
    public function setManager(Manager $manager): Action
    {
        $this->manager = $manager;

        return $this;
    }
}
