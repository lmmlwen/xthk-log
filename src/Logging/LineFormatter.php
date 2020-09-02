<?php

namespace Lmmlwen\Xthklog\Logging;

use Illuminate\Support\Str;
use Monolog\Formatter\LineFormatter as BaseLineFormatter;
use Monolog\Formatter\NormalizerFormatter;

class LineFormatter extends BaseLineFormatter
{
    const NEW_SIMPLE_FORMAT = "%channel%.%level_name%:[%datetime%] [%request_id%] [msg:%message%] [context:%context%][extra:%extra%]\n";
    public static $logId;

    public function format(array $record)
    {
        $output = self::NEW_SIMPLE_FORMAT;
        $vars = (new NormalizerFormatter())->format($record);
        $vars['request_id'] = 'request_id:' . self::getLogId();
        foreach ($vars['extra'] as $var => $val) {
            if (false !== strpos($output, '%extra.' . $var . '%')) {
                $output = str_replace('%extra.' . $var . '%', $this->stringify($val), $output);
                unset($vars['extra'][$var]);
            }
        }
        if (isset($vars['context']['exception']) && !empty($vars['context']['exception'])) {
            $vars['message'] = '';
            $vars['context'] = $vars['context']['exception'];
            if (isset($vars['context']['trace'])) {
                unset($vars['context']['trace']);
            }
            if (isset($vars['context']['previous'])) {
                unset($vars['context']['previous']);
            }
        }

        if (false !== strpos($output, '%')) {
            $output = preg_replace('/%(?:extra|context)\..+?%/', '', $output);
        }

        foreach ($vars as $var => $val) {
            if (false !== strpos($output, '%' . $var . '%')) {
                $output = str_replace('%' . $var . '%', $this->stringify($val), $output);
            }
        }
        // remove leftover %extra.xxx% and %context.xxx% if any
        if (false !== strpos($output, '%')) {
            $output = preg_replace('/%(?:extra|context)\..+?%/', '', $output);
        }

        return $output;
    }

    static function setLogId($logId)
    {
        if (!empty($logId)) {
            self::$logId = $logId;
        } else {
            self::$logId = self::newLogIDInt();
        }
    }

    static function getLogId()
    {
        return self::$logId;
    }

    static function newLogIDInt()
    {
        $data = Str::uuid();
        return substr($data->getInteger(), 0, 15);
    }
}