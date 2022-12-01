<?php
namespace ExceptionHandler\Handler;

use Throwable;

/**
 * Thinkphp的异常处理
 * Class ThinkphpExceptionHandler
 * @package Topnew\Handler
 */
class ThinkphpExceptionHandler extends CommonExceptionHandler
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
                'GET Data'              => $request ? $request->get() : $_GET,
                'POST Data'             => $request ? $request->post() : $_POST,
                'Files'                 => $request ? $request->file() : $_FILES,
                'Cookies'               => $_COOKIE,
                'Session'               => isset($_SESSION) ? $_SESSION : [],
                'Server/Request Data'   => $_SERVER,
                'Environment Variables' => $_ENV,
                'ThinkPHP Constants'    => $this->getConst(),
            ]
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

    /**
     * 获取常量列表
     * @access private
     * @return array 常量列表
     */
    protected function getConst()
    {
        $const = get_defined_constants(true);

        return $const['user'] ?? [];
    }
}