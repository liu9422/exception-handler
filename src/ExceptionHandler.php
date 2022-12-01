<?php
namespace ExceptionHandler;

use ExceptionHandler\Handler\EasySwooleExceptionHandler;
use ExceptionHandler\Handler\ThinkphpExceptionHandler;
use ExceptionHandler\Handler\WebmanExceptionHandler;
use Throwable;
use BadMethodCallException;
use ExceptionHandler\Handler\CommonExceptionHandler;
use ExceptionHandler\Reporter\Config;
use ExceptionHandler\Reporter\Email\EmailReporter;
use ExceptionHandler\Reporter\Reporter;

/**
 * Class ExceptionHandler
 * @package Topnew\ExceptionHandler
 */
class ExceptionHandler
{
    protected $handler = [
        'ThinkPHP' => ThinkphpExceptionHandler::class,
        'EasySwoole' => EasySwooleExceptionHandler::class,
        'Webman' => WebmanExceptionHandler::class,
    ];

    protected $reporter;

    protected $structName  = 'ThinkPHP';

    /**
     * ExceptionHandler constructor.
     */
    public function __construct()
    {
        # 默认使用Email报告
        $this->reporter = new EmailReporter();
    }

    /**
     * @param Throwable $exception
     * @param $request
     * @param Config $config
     * @return mixed
     */
    public function renderException(Throwable $exception, $request, Config $config)
    {
        $handler = $this->getHandler($this->structName);
        $html = $handler->renderExceptionHtml($exception, $request);
        $config->setBody($html);
        return $this->getReporter()->send($config);
    }

    /**
     * @param Reporter $reporter
     */
    public function setReporter(Reporter $reporter)
    {
        $this->reporter = $reporter;
    }

    /**
     * @return Reporter
     */
    public function getReporter()
    {
        return $this->reporter;
    }

    /**
     * @param $structName
     */
    public function setStructName($structName)
    {
        $this->structName = $structName;
    }

    /**
     * @param string $structName
     * @return CommonExceptionHandler
     */
    private function getHandler(string $structName)
    {
        $class = $this->handler[$structName] ?? '';
        if($class){
            return new $class;
        }else{
            throw new BadMethodCallException('no exception handler!');
        }
    }
}