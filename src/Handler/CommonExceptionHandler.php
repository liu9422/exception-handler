<?php
namespace ExceptionHandler\Handler;

use Throwable;

/**
 * Class CommonExceptionHandler
 * @package Topnew\ExceptionHandler\Handler
 */
abstract class CommonExceptionHandler
{
    /**
     *  获取异常的HTML页面
     * @param Throwable $exception
     * @param $request
     * @return mixed
     */
    abstract public function renderExceptionHtml(Throwable $exception, $request);
}