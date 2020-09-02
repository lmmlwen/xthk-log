<?php

namespace Lmmlwen\Xthklog\Middleware;

use Lmmlwen\Xthklog\Logging\LineFormatter;
use Closure;

class LogMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $logId = $request->header("XT_LOGID");
        if (empty($logId)) {
            $logId = $request->input("request_id");
        }
        LineFormatter::setLogId($logId);
        return $next($request);
    }
}