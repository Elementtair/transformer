<?php
/**
 * This file is part of the ForkBB <https://github.com/forkbb>.
 *
 * @copyright (c) Visman <mio.visman@yandex.ru, https://github.com/MioVisman>
 * @license   The MIT License (MIT)
 */

declare(strict_types=1);

namespace ForkBB\Models;

use ForkBB\Models\Model;

class DataModel extends Model
{
    /**
     * Массив флагов измененных свойств модели
     * @var array
     */
    protected $zModFlags = [];

    /**
     * Массив состояний отслеживания изменений в свойствах модели
     * @var array
     */
    protected $zTrackFlags = [];

    /**
     * Устанавливает значения для свойств
     * Сбрасывает вычисленные свойства
     * Флаги модификации свойст сброшены
     */
    public function setAttrs(array $attrs): Model
    {
        $this->zModFlags   = [];
        $this->zTrackFlags = [];

        return parent::setAttrs($attrs);
    }

    /**
     * Перезаписывает свойства модели
     * Флаги модификации свойств сбрасываются/устанавливаются в зависимости от второго параметра
     */
    public function replAttrs(array $attrs, bool $setFlags = false): DataModel
    {
        foreach ($attrs as $name => $value) {
            $this->__set($name, $value);

            if (! $setFlags) {
                unset($this->zModFlags[$name]);
            }
        }

        return $this;
    }

    /**
     * Возвращает значения свойств в массиве
     */
    public function getAttrs(): array
    {
        return $this->zAttrs; //????
    }

    /**
     * Возвращает массив имен измененных свойств модели
     */
    public function getModified(): array
    {
        return \array_keys($this->zModFlags);
    }

    /**
     * Возвращает модифицировано ли свойство модели
     */
    public function isModified(string $name): bool
    {
        return isset($this->zModFlags[$name]);
    }

    /**
     * Обнуляет массив флагов измененных свойств модели
     */
    public function resModified(): void
    {
        $this->zModFlags   = [];
        $this->zTrackFlags = [];
    }

    /**
     * Устанавливает значение для свойства
     */
    public function __set(string $name, /* mixed */ $value): void
    {
        // без отслеживания
        if (0 === \strpos($name, '__')) {
            $track = null;
            $name  = \substr($name, 2);
        // с отслеживанием
        } else {
            $track = false;
            if (\array_key_exists($name, $this->zAttrs)) {
                $track = true;
                $old   = $this->zAttrs[$name];
                // fix
                if (
                    \is_int($value)
                    && \is_numeric($old)
                    && \is_int(0 + $old)
                ) {
                    $old = (int) $old;
                }
            }
        }

        $this->zTrackFlags[$name] = $track;

        parent::__set($name, $value);

        unset($this->zTrackFlags[$name]);

        if (null === $track) {
            return;
        }

        if (
            (
                ! $track
                && \array_key_exists($name, $this->zAttrs)
            )
            || (
                $track
                && $old !== $this->zAttrs[$name]
            )
        ) {
            $this->zModFlags[$name] = true;

            if (isset($this->zDepend[$name])) {
                foreach ($this->zDepend[$name] as $dependent) {
                    $this->zModFlags[$dependent] = true; //???? может только физические свойства менять?
                }
            }
        }
    }

    /**
     * Возвращает значение свойства
     */
    public function __get(string $name) /* : mixed */
    {
        // без вычисления
        if (0 === \strpos($name, '__')) {
            return $this->getAttr(\substr($name, 2));
        // с вычислениями
        } else {
            return parent::__get($name);
        }
    }

    /**
     * Удаляет свойство ????
     */
    public function __unset(/* mixed */ $name): void
    {
        $this->zModFlags[$name] = false;

        parent::__unset($name);
    }
}
