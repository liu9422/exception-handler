<?php
namespace ExceptionHandler\Reporter;

abstract class Reporter
{
    abstract public function send(Config $config);
}