<?php
namespace ExceptionHandler\Handler;

use Throwable;

/**
 * EasySwoole的异常处理
 * Class EasySwooleExceptionHandler
 * @package Topnew\Handler
 */
class EasySwooleExceptionHandler extends CommonExceptionHandler
{
    /**
     * 获取异常的HTML页面
     * @param Throwable $exception
     * @param $request
     * @return false|string
     */
    public function renderExceptionHtml(Throwable $exception, $request)
    {
        $data = [
            'name'    => get_class($exception),
            'file'    => $exception->getFile(),
            'line'    => $exception->getLine(),
            'message' => $exception->getMessage(),
            'trace'   => $exception->getTrace(),
            'code'    => $exception->getCode(),
            'source'  => $this->getSourceCode($exception),
            'tables'  => [
                'GET Data'              => $request ? $request->getSwooleRequest()->get : [],
                'POST Data'             => $request ? $request->getSwooleRequest()->post : [],
                'Body'                  => $request ? $request->getSwooleRequest()->getContent() : [],
                'Files'                 => $request ? $request->getSwooleRequest()->files : [],
                'Cookies'               => $request ? $request->getCookieParams() : [],
                'Server/Request Data'   => $request ? $request->getSwooleRequest()->server : $_SERVER,
                'Environment Variables' => $this->getEnvironmentVariables(),
            ],
        ];

        while (ob_get_level() > 1) {
            ob_end_clean();
        }

        $data['echo'] = ob_get_clean();

        ob_start();
        extract($data);

        include __DIR__ . '/../Tpl/tpl.php';

        return ob_get_clean();
    }

    /**
     * 获取Swoole环境参数
     * @return array
     */
    protected function getEnvironmentVariables()
    {
        $cpuNumber = 0;
        if(function_exists('swoole_cpu_num')){
            $cpuNumber = swoole_cpu_num();
        }
        $version = '';
        if(function_exists('swoole_version')){
            $version = swoole_version();
        }
        return [
            'CPU Num' => $cpuNumber,
            'PHP Version' =>  phpversion(),
            'Swoole Version' => $version
        ];
    }

    /**
     * 获取源代码文件
     * @param Throwable $exception
     * @return array
     */
    protected function getSourceCode(Throwable $exception)
    {
        $line  = $exception->getLine();
        $first = ($line - 9 > 0) ? $line - 9 : 1;

        try {
            $contents = file($exception->getFile());
            $source   = [
                'first'  => $first,
                'source' => array_slice($contents, $first - 1, 19),
            ];
        } catch (Throwable $e) {
            $source = [];
        }

        return $source;
    }
}