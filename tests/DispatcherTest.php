<?php
/**
 * Simple dispatcher class
 *
 * PHP version 5.2
 *
 * Copyright (c) 2009-2010 Shinya Ohyanagi, All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of Shinya Ohyanagi nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @category  Net
 * @package   Net_URL_Dispatcher
 * @version   $id$
 * @copyright 2009-2010 Shinya Ohyanagi
 * @author    Shinya Ohyanagi <sohyanagi@gmail.com>
 * @license   New BSD License
 */

/**
 * @see prepare
 */
require_once 'prepare.php';

/**
 * DescribeDispatcher
 *
 * @category  Net
 * @package   Net_URL_Dispatcher
 * @version   $id$
 * @copyright 2009-2010 Shinya Ohyanagi
 * @author    Shinya Ohyanagi <sohyanagi@gmail.com>
 * @license   New BSD License
 */
class Net_URL_DispatcherTest extends PHPUnit_Framework_TestCase
{
    public function testShouldGetVersion()
    {
        $this->assertSame(Net_URL_Dispatcher::VERSION, '0.2');
    }

    public function testShouldCreateInstance()
    {
        $dispatcher = new Net_URL_Dispatcher();
        $this->assertTrue($dispatcher instanceof Net_URL_Dispatcher);
    }

    public function testShouldSetDirecotryPath()
    {
        $path       = dirname(__FILE__) . '/apps/sample/';
        $dispatcher = new Net_URL_Dispatcher(__METHOD__);
        $dispatcher->setDirectory($path);
        $exceptPath = $dispatcher->getDirectory();

        $this->assertSame($exceptPath, $path);
    }

    public function testShouldSetDirecotryPathInDispatchMethod()
    {
        $path       = dirname(__FILE__) . '/apps/sample/';
        $dispatcher = new Net_URL_Dispatcher(__METHOD__);
        $_ENV['PATH_INFO'] = 'hoge/index';

        ob_start();
        $dispatcher->connect(':controller/:action')->dispatch($path);
        $buffer = ob_get_contents();
        ob_end_clean();

        $this->assertSame($dispatcher->getDirectory(), $path);
    }

    public function testShouldDefaultErrorReportingBeE_all()
    {
        $dispatcher = new Net_URL_Dispatcher(__METHOD__);
        $this->assertSame(error_reporting(), E_ALL);
    }

    public function testShouldSetErrorReporting()
    {
        $dispatcher = new Net_URL_Dispatcher();
        $dispatcher->setErrorReporting(E_ALL|E_NOTICE);

        $this->assertSame(error_reporting(), E_ALL|E_NOTICE);

    }

    public function testShouldSetParams()
    {
        $path       = dirname(__FILE__) . '/apps/sample/';
        $dispatcher = new Net_URL_Dispatcher(__METHOD__);
        $_ENV['PATH_INFO'] = 'hoge/mix/hoge/fuga';

        ob_start();
        $dispatcher->setParams(array('foo' => 'bar'))->connect(':controller/:action/*params')->dispatch($path);
        $buffer = ob_get_contents();
        ob_end_clean();

        $this->assertSame($buffer, 'fuga_bar');
    }

    public function testShouldSetCustomParam()
    {
        $path       = dirname(__FILE__) . '/apps/sample/';
        $dispatcher = new Net_URL_Dispatcher(__METHOD__);
        $_ENV['PATH_INFO'] = 'hoge/getid/1';
        ob_start();
        $dispatcher->connect(':controller/:action/:id')->dispatch($path);
        $buffer = ob_get_contents();
        ob_end_clean();
        $this->assertSame($buffer, 'id=1');

        $_ENV['PATH_INFO'] = 'hoge/customparams/1/foo';
        ob_start();
        $dispatcher->connect(':controller/:action/:id/:name')->dispatch($path);
        $buffer = ob_get_contents();
        ob_end_clean();
        $this->assertSame($buffer, 'id=1, name=foo');
    }

    public function testShouldSetCustomParams()
    {
        $path       = dirname(__FILE__) . '/apps/sample/';
        $dispatcher = new Net_URL_Dispatcher(__METHOD__);
        $_ENV['PATH_INFO'] = 'hoge/customparams/1/name/foo';
        ob_start();
        $dispatcher->connect(':controller/:action/:id/*params')->dispatch($path);
        $buffer = ob_get_contents();
        ob_end_clean();
        $this->assertSame($buffer, 'id=1, name=foo');

        ob_start();
        $dispatcher->connect(':controller/:action/*params')->dispatch($path);
        $buffer = ob_get_contents();
        ob_end_clean();
        $this->assertSame($buffer, 'id=1, name=foo');
    }

    public function testShouldSetCustomParamAfterWildcard()
    {
        $path       = dirname(__FILE__) . '/apps/sample/';
        $dispatcher = new Net_URL_Dispatcher(__METHOD__);
        $_ENV['PATH_INFO'] = 'hoge/customparams/name/foo/1';
        ob_start();
        $dispatcher->connect(':controller/:action/*params/:id')->dispatch($path);
        $buffer = ob_get_contents();
        ob_end_clean();
        $this->assertSame($buffer, 'id=1, name=foo');
    }

    public function testShouldCallNet_url_mapperClassMethodWhichHasRetrurnValue()
    {
        $path       = dirname(__FILE__) . '/apps/sample/';
        $dispatcher = new Net_URL_Dispatcher(__METHOD__);
        $buffer     = $dispatcher->getId();

        $this->assertSame($buffer, __METHOD__);
    }

    public function testShouldCallNet_url_mapperClassMethodWhichHasNoReturnValue()
    {
        $path       = dirname(__FILE__) . '/apps/sample/';
        $dispatcher = new Net_URL_Dispatcher(__METHOD__);
        $buffer     = $dispatcher->setScriptname('index.php');

        $this->assertTrue($buffer instanceof Net_URL_Dispatcher);
    }

    public function testShouldSetControllerDirectoryName()
    {
        $path       = dirname(dirname(__FILE__)) . '/apps/sample/';
        $dispatcher = new Net_URL_Dispatcher(__METHOD__);
        $dispatcher->setControllerDirectoryName('foo');
        $directroy  = $dispatcher->getControllerDirectoryName();

        $this->assertSame('foo', $directroy);
    }

    public function testShouldGetControllerDirectroryName()
    {
        $path       = dirname(dirname(__FILE__)) . '/apps/sample/';
        $dispatcher = new Net_URL_Dispatcher(__METHOD__);
        $dispatcher->setControllerDirectoryName('bar');
        $directroy  = $dispatcher->getControllerDirectoryName();

        $this->assertSame('bar', $directroy);
    }

    public function testShouldGetDefaultControllerDirectroyName()
    {
        $path       = dirname(dirname(__FILE__)) . '/apps/sample/';
        $dispatcher = new Net_URL_Dispatcher(__METHOD__);
        $directroy  = $dispatcher->getControllerDirectoryName();

        $this->assertSame('controllers', $directroy);
    }

    public function testShouldSetActionDirectoryName()
    {
        $path       = dirname(dirname(__FILE__)) . '/apps/sample/';
        $dispatcher = new Net_URL_Dispatcher(__METHOD__);
        $dispatcher->setActionDirectoryName('foo');
        $directroy  = $dispatcher->getActionDirectoryName();

        $this->assertSame('foo', $directroy);
    }

    public function testShouldGetActionDirectroryName()
    {
        $path       = dirname(dirname(__FILE__)) . '/apps/sample/';
        $dispatcher = new Net_URL_Dispatcher(__METHOD__);
        $dispatcher->setActionDirectoryName('bar');
        $directroy  = $dispatcher->getActionDirectoryName();

        $this->assertSame('bar', $directroy);
    }

    public function testShouldGetDefaultActionDirectroyName()
    {
        $path       = dirname(dirname(__FILE__)) . '/apps/sample/';
        $dispatcher = new Net_URL_Dispatcher(__METHOD__);
        $directroy  = $dispatcher->getActionDirectoryName();

        $this->assertSame('actions', $directroy);
    }
}
