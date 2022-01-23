<?php
/**
 * This file is part of the ForkBB <https://github.com/forkbb>.
 *
 * @copyright (c) Visman <mio.visman@yandex.ru, https://github.com/MioVisman>
 * @license   The MIT License (MIT)
 */

declare(strict_types=1);

namespace ForkBB;

use ForkBB\Core\Container;
use ForkBB\Core\ErrorHandler;
use ForkBB\Models\Page;
use RuntimeException;

\error_reporting(\E_ALL ^ \E_NOTICE);
\ini_set('display_errors', '0');
\ini_set('log_errors', '1');

\setlocale(\LC_ALL, 'C');
\mb_language('uni');
\mb_internal_encoding('UTF-8');
\mb_substitute_character(0xFFFD);

define('FORK_GROUP_UNVERIFIED', 0);
define('FORK_GROUP_ADMIN', 1);
define('FORK_GROUP_MOD', 2);
define('FORK_GROUP_GUEST', 3);
define('FORK_GROUP_MEMBER', 4);

define('TRANSFORMER_MOVE', 0);
define('TRANSFORMER_MERGE', 1);
define('TRANSFORMER_COPY', 2);

require __DIR__ . '/../vendor/autoload.php';

$errorHandler = new ErrorHandler();

if (\is_file(__DIR__ . '/config/main.php')) {
    $c = new Container(include __DIR__ . '/config/main.php');
} elseif (\is_file(__DIR__ . '/config/install.php')) {
    $c = new Container(include __DIR__ . '/config/install.php');
} else {
    throw new RuntimeException('Application is not configured');
}

$errorHandler->setContainer($c);

require __DIR__ . '/functions.php';
\ForkBB\_init($c);

// https or http?
if (
    ! empty($_SERVER['HTTPS'])
    && 'off' !== \strtolower($_SERVER['HTTPS'])
) {
    $c->BASE_URL = \str_replace('http://', 'https://', $c->BASE_URL);
} else {
    $c->BASE_URL = \str_replace('https://', 'http://', $c->BASE_URL);
}
$c->PUBLIC_URL = $c->BASE_URL . $forkPublicPrefix;

$c->FORK_REVISION = 48;
$c->START         = $forkStart;
$c->DIR_APP       = __DIR__;
$c->DIR_PUBLIC    = \realpath(__DIR__ . '/../public');
$c->DIR_CACHE     = __DIR__ . '/cache';
$c->DIR_VIEWS     = __DIR__ . '/templates';
$c->DIR_LANG      = __DIR__ . '/lang';
$c->DIR_LOG       = __DIR__ . '/log';
$c->DATE_FORMATS  = ['Y-m-d', 'd M Y', 'Y-m-d', 'Y-d-m', 'd-m-Y', 'm-d-Y', 'M j Y', 'jS M Y'];
$c->TIME_FORMATS  = ['H:i:s', 'H:i', 'H:i:s', 'H:i', 'g:i:s a', 'g:i a'];

$controllers = ['Primary', 'Routing'];
foreach ($controllers as $controller) {
    $page = $c->{$controller};

    if ($page instanceof Page) {
        break;
    }
}

if (null !== $page->onlinePos) {
    $c->Online->calc($page);
}

$tpl = $c->View->rendering($page);

if (
    $c->isInit('DB')
    && $c->DB->inTransaction()
) {
    $c->DB->commit();
}

if (
    null !== $tpl
    && $c->DEBUG > 0
) {
    $debug = $c->View->rendering($c->Debug->debug());
    $tpl   = \str_replace('<!-- debuginfo -->', $debug, $tpl);
}

exit($tpl);
