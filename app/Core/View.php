<?php
/**
 * This file is part of the ForkBB <https://github.com/forkbb>.
 *
 * @copyright (c) Visman <mio.visman@yandex.ru, https://github.com/MioVisman>
 * @license   The MIT License (MIT)
 */

declare(strict_types=1);

namespace ForkBB\Core;

use R2\Templating\Dirk;
use ForkBB\Models\Page;
use RuntimeException;

class View extends Dirk
{
    public function __construct (string $cache, string $views)
    {
        $config = [
            'views'     => $views,
            'cache'     => $cache,
            'ext'       => '.forkbb.php',
            'echo'      => '\\htmlspecialchars((string) %s, \\ENT_HTML5 | \\ENT_QUOTES | \\ENT_SUBSTITUTE, \'UTF-8\')',
            'separator' => '/',
        ];
        $this->compilers[] = 'Transformations';

        parent::__construct($config);
    }

    /**
     * Трансформация скомпилированного шаблона
     */
    protected function compileTransformations(string $value): string
    {
        if (\str_starts_with($value, '<?xml ')) {
            $value = \str_replace(' \\ENT_HTML5 | \\ENT_QUOTES | \\ENT_SUBSTITUTE,', ' \\ENT_XML1,', $value);
        }

        $perfix = <<<'EOD'
<?php

declare(strict_types=1);

use function \ForkBB\{__, num, dt, size};

?>
EOD;

        if (false === \strpos($value, '<!-- inline -->')) {
            return $perfix . $value;
        }

        return $perfix . \preg_replace_callback(
            '%<!-- inline -->([^<]*(?:<(?!!-- endinline -->)[^<]*)*+)(?:<!-- endinline -->)?%',
            function ($matches) {
                return \preg_replace('%\h*\R\s*%', '', $matches[1]);
            },
            $value
        );
    }

    /**
     * Return result of templating
     */
    public function rendering(Page $p): ?string
    {
        if (null === $p->nameTpl) {
            $this->sendHttpHeaders($p);

            return null;
        }

        $p->prepare();

        $this->templates[] = $p->nameTpl;
        while ($_name = \array_shift($this->templates)) {
            $this->beginBlock('content');
            foreach ($this->composers as $_cname => $_cdata) {
                if (\preg_match($_cname, $_name)) {
                    foreach ($_cdata as $_citem) {
                        \extract((\is_callable($_citem) ? $_citem($this) : $_citem) ?: []);
                    }
                }
            }
            require($this->prepare($_name));
            $this->endBlock(true);
        }

        $this->sendHttpHeaders($p);

        return $this->block('content');
    }

    /**
     * Compile echos
     */
    protected function compileEchos(string $value): string
    {
        $value = \preg_replace_callback(
            '%(@)?\{\{!\s*(.+?)\s*!\}\}(\r?\n)?%s',
            function($matches) {
                $whitespace = empty($matches[3]) ? '' : $matches[3] . $matches[3];

                return $matches[1]
                    ? \substr($matches[0], 1)
                    : '<?= \\htmlspecialchars((string) '
                        . $this->compileEchoDefaults($matches[2])
                        . ', \\ENT_HTML5 | \\ENT_QUOTES | \\ENT_SUBSTITUTE, \'UTF-8\', false) ?>'
                        . $whitespace;
            },
            $value
        );

        return parent::compileEchos($value);
    }

    /**
     * Отправляет HTTP заголовки
     */
    protected function sendHttpHeaders(Page $p): void
    {
        foreach ($p->httpHeaders as $catHeader) {
            foreach ($catHeader as $header) {
                \header($header[0], $header[1]);
            }
        }
    }
}
