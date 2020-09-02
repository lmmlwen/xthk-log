<?php

namespace Lmmlwen\Xthklog\Logging;

class XthkLogFormatter
{
    public function __invoke($logger)
    {
        foreach ($logger->getHandlers() as $handler) {
            $handler->setFormatter(new LineFormatter());
        }
    }
}