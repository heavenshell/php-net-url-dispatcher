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
 * @see Net_URL_Dispatcher_Exception
 */
require_once 'Net/URL/Dispatcher/Exception.php';

/**
 * Net_URL_Dispatcher
 *
 * @category  Net
 * @package   Net_URL_Dispatcher
 * @version   $id$
 * @copyright 2009-2010 Shinya Ohyanagi
 * @author    Shinya Ohyanagi <sohyanagi@gmail.com>
 * @license   New BSD License
 */
class Net_URL_Dispatcher
{
    /**
     * Net_URL_Dispatcher VERSION
     */
    const VERSION = '0.2';

    /**
     * Router
     *
     * @var    mixed
     * @access protected
     */
    protected $_router = null;

    /**
     * Path to controller directory
     *
     * @var    mixed
     * @access protected
     */
    protected $_directory = null;

    /**
     * Path info
     *
     * @var    mixed
     * @access protected
     */
    protected $_pathInfo = null;

    /**
     * Net_URL_Mapper requested instance id
     *
     * @var    mixed
     * @access private
     */
    private $_mapperId = null;

    /**
     * Params
     *
     * @var    mixed
     * @access private
     */
    private $_params = null;

    /**
     * Controller directory name
     *
     * @var    mixed
     * @access private
     */
    private $_controllerDirectryName = null;

    /**
     * Action directory name
     *
     * @var    mixed
     * @access private
     */
    private $_actionDirectoryName = null;

    /**
     * Constructor
     *
     * @param  string $id Requested instance name
     * @access public
     * @return void
     */
    public function __construct($id = '__default__')
    {
        spl_autoload_register(array('Net_URL_Dispatcher', 'load'));
        $this->_mapperId = $id;
        $this->_setRouter();
        $this->setErrorReporting();
    }

    /**
     * Call Net_URL_Mapper's method
     *
     * @param  mixed $method Net_URL_Mapper's method name
     * @param  mixed $args arguments
     * @access public
     * @return mixed Net_URL_Dispatcher Fluent interface or result of method
     */
    public function __call($method, $args = null)
    {
        $ret = null;
        if (method_exists($this->_router, $method)) {
            $ret = call_user_func_array(array($this->_router, $method), $args);
            if (!is_null($ret)) {
                return $ret;
            }
        }

        return $this;
    }

    /**
     * Get params
     *
     * @access public
     * @return void
     */
    public function getParams()
    {
        return $this->_params;
    }

    /**
     * Set params
     *
     * @param  array $value
     * @access public
     * @return Net_URL_Dispatcher Fluent interface
     */
    public function setParams(array $value)
    {
        $this->_params = $value;
        return $this;
    }

    /**
     * Get controller directory name
     *
     * @access public
     * @return string Controller directory name
     */
    public function getControllerDirectoryName()
    {
        if (is_null($this->_controllerDirectryName)) {
            $this->_controllerDirectryName = 'controllers';
        }
        return $this->_controllerDirectryName;
    }

    /**
     * Set controller directory name
     *
     * @param  mixed $value Controller path name
     * @access public
     * @return Net_URL_Dispatcher Fluent interface
     */
    public function setControllerDirectoryName($value)
    {
        $this->_controllerDirectryName = $value;
        return $this;
    }

    /**
     * Get action directory name
     *
     * @access public
     * @return string Action directory name
     */
    public function getActionDirectoryName()
    {
        if (is_null($this->_actionDirectoryName)) {
            $this->_actionDirectoryName = 'actions';
        }

        return $this->_actionDirectoryName;
    }

    /**
     * Set action directory name
     *
     * @param  mixed $value Action directory name
     * @access public
     * @return Net_URL_Dispatcher Fluent interface
     */
    public function setActionDirectoryName($value)
    {
        $this->_actionDirectoryName = $value;
        return $this;
    }

    /**
     * Set error reporting
     *
     * @param  mixed $value Error reporting
     * @access public
     * @return Net_URL_Dispatcher Fluent interface
     */
    public function setErrorReporting($value = null)
    {
        if (is_null($value)) {
            error_reporting(E_ALL);
        } else {
            error_reporting($value);
        }

        return $this;
    }

    /**
     * Set router
     *
     * @access protected
     * @return Net_URL_Dispatcher Fluent interface
     */
    protected function _setRouter()
    {
        require_once 'Net/URL/Mapper.php';
        $this->_router = Net_URL_Mapper::getInstance($this->_mapperId);
        return $this;
    }

    /**
     * Parses a path and creates a connection
     *
     * @param  mixed $path The path to connect
     * @param  array $defaults Default values for path parts
     * @param  array $rules Regular expressions for path parts
     * @access public
     * @return Net_URL_Dispatcher Fluent interface
     */
    public function connect($path = null, $defaults = array(), $rules = array())
    {
        if (is_null($this->_router)
                && !$this->_router instanceof Net_URL_Mapper) {
            throw new Net_URL_Dispatcher_Exception('Router is invalid.');
        }

        if (is_null($path)) {
            $path = ':controller/:action/*params';
        }

        $controller = null;
        if (is_string($path)) {
            $map = explode('/', $path);
            if (substr($map[0], 0, 1) !== ':') {
                $controller = $map[0];
            }
        }

        if (is_array($path)) {
            $map = '';
            if (isset($path['controller'])) {
                $map = $path['controller'];
            }
            if (isset($path['action'])) {
                $map = $map . '/' . $path['action'];
            }
            $path = $map;
        }

        if (count($defaults) === 0) {
            $defaults = array(
                'controller' => $controller,
                'action'     => 'index',
                'params'     => null
            );
        }

        $this->_router->connect($path, $defaults, $rules);
        return $this;
    }

    /**
     * Matching url to path
     *
     * @param  mixed $path
     * @access protected
     * @return mixed Result of matching
     */
    protected function _match($path)
    {
        return $this->_router->match($path);
    }

    /**
     * Set directory
     *
     * @param  mixed $path
     * @access public
     * @return Net_URL_Dispatcher Fluent interface
     */
    public function setDirectory($path)
    {
        $this->_directory = $path;
        return $this;
    }

    /**
     * Get directory
     *
     * @access public
     * @return string Path to directory
     */
    public function getDirectory()
    {
        return $this->_directory;
    }

    /**
     * Set pathinfo
     *
     * @param  mixed $pathInfo Pathinfo
     * @access public
     * @return Net_URL_Dispatcher Fluent interface
     */
    public function setPathInfo($pathInfo)
    {
        $this->_pathInfo = $pathInfo;
        return $this;
    }

    /**
     * Get pathinfo
     *
     * @access public
     * @return mixed Pathinfo
     */
    public function getPathInfo()
    {
        $env = (isset($_ENV['PATH_INFO'])) ? $_ENV['PATH_INFO'] : getEnv('PATH_INFO');
        return is_null($this->_pathInfo) ? ltrim($env, '/') : $this->_pathInfo;
    }

    /**
     * Dispatch to controller or action
     *
     * @param  mixed $directory Path to controllers or actions
     * @access public
     * @throws Net_URL_Dispatcher_Exception
     * @return Net_URL_Dispatcher Fluent interface
     */
    public function dispatch($directory = null)
    {
        if (is_null($directory)) {
            $directory = rtrim($this->_directory, '\//') . DIRECTORY_SEPARATOR;
        } else {
            $directory = rtrim($directory, '\//') . DIRECTORY_SEPARATOR;
            $this->_directory = $directory;
        }

        if (!is_dir($directory)) {
            throw new Net_URL_Dispatcher_Exception('Directory found.');
        }

        $path  = $this->getPathInfo();
        $match = $this->_match($path);

        if (is_null($match)) {
            throw new Net_URL_Dispatcher_Exception('Could not dispatch.');
        }

        // Get controller
        $controller = isset($match['controller'])
                    ? ucfirst($match['controller']) . 'Controller'
                    : null;

        // Get action
        $action = isset($match['action'])
                ? $match['action'] . 'Action'
                : null;

        // Get patameters
        $params = isset($match['params'])
                ? $match['params']
                : null;

        $params = $this->parseRequests($params);
        $params = self::deleteNullByte($params);

        unset($match['controller']);
        unset($match['action']);
        unset($match['params']);

        foreach ($match as $key => $value) {
            $params[$key] = $value;
        }

        if (!is_null($this->_params)) {
            if (is_array($this->_params) && is_array($params)) {
                $params = array_merge($params, $this->_params);
            }
        }

        // If $controller is null, use action as class.
        if (is_null($controller)) {
            $this->dispatchAction(ucfirst($action), $params, $directory);
        } else {
            $this->dispatchController($controller, $action, $params, $directory);
        }

        return $this;
    }

    /**
     * Dispatch to controller class
     *
     * @param  mixed $controller Controller name
     * @param  mixed $action Action name
     * @param  mixed $params Parameters
     * @param  mixed $directory Path to controllers
     * @access public
     * @throws Net_URL_Dispatcher_Exception
     * @return Net_URL_Dispatcher_Controller
     */
    public function dispatchController($controller, $action, $params, $directory = null)
    {
        if (!class_exists($controller, false)) {
            $controllerPath = $directory . $this->getControllerDirectoryName()
                            . DIRECTORY_SEPARATOR . $controller . '.php';

            self::securityCheck($controllerPath);
            if (!is_file($controllerPath)) {
                throw new Net_URL_Dispatcher_Exception('File not found.');
            }
            require_once $controllerPath;
        }

        // Create instance
        $instance = new $controller;

        if (!method_exists($instance, $action)) {
            throw new Net_URL_Dispatcher_Exception('Action metod not found.');
        }

        if (method_exists($instance, 'setParams')) {
            $instance->setParams($params);
        }

        // Execute preDispatch
        if (method_exists($instance, 'preDispatch')) {
            $instance->preDispatch();
        }

        // Execute action
        $instance->{$action}();

        // Execute postDispatch
        if (method_exists($instance, 'postDispatch')) {
            $instance->postDispatch();
        }

        if (method_exists($instance, 'getActionStack')) {
            $actionStack = $instance->getActionStack();
            if (isset($actionStack['controller'])
                    && !is_null($actionStack['controller'])) {
                $controller = $actionStack['controller'] . 'Controller';
            }

            if (isset($actionStack['action'])) {
                $action = $actionStack['action'] . 'Action';
            }

            if (isset($actionStack['params'])) {
                if (is_array($params)) {
                    $params = array_merge($params, $actionStack['params']);
                } else {
                    $params = $actionStack['params'];
                }
            }

            if (!is_null($actionStack)) {
                $this->dispatchController($controller, $action, $params, $directory);
            }
        }

        return $instance;
    }

    /**
     * Dispatch to action
     *
     * @param  mixed $action Action name
     * @param  mixed $params Parameters
     * @param  mixed $directory Path to directory
     * @access public
     * @throws Net_URL_Dispatcher_Exception
     * @return Net_URL_Dispatcher_Action Fluent interface
     */
    public function dispatchAction($action, $params, $directory = null)
    {
        if (!class_exists($action, false)) {
            $actionPath = $directory . $this->getActionDirectoryName()
                        . DIRECTORY_SEPARATOR . $action . '.php';

            self::securityCheck($actionPath);
            if (!is_file($actionPath)) {
                throw new Net_URL_Dispatcher_Exception('File not found.');
            }
            require_once $actionPath;
        }

        // Create instance
        $instance = new $action;

        if (method_exists($instance, 'setParams')) {
            $instance->setParams($params);
        }

        // Before Execute
        if (method_exists($instance, 'preExecute')) {
            $instance->preExecute();
        }

        // Execute
        if (method_exists($instance, 'execute')) {
            $instance->execute();
        }

        // After Exectute
        if (method_exists($instance, 'postExecute')) {
            $instance->postExecute();
        }

        if (method_exists($instance, 'getActionStack')) {
            $actionStack = $instance->getActionStack();

            if (isset($actionStack['action'])) {
                if (isset($actionStack['params'])
                        && is_array($actionStack['params'])) {
                    if (is_array($params)) {
                        $params = array_merge($params, $actionStack['params']);
                    } else {
                        $params = $actionStack['params'];
                    }
                }

                $stackAction = $actionStack['action'] . 'Action';
                $this->dispatchAction($stackAction, $params, $directory);
            }
        }

        return $instance;
    }

    /**
     * Parse requests
     *
     * @param  mixed $value Path info
     * @access public
     * @return mixed array or null
     */
    public function parseRequests($value = null)
    {
        $requests = array();
        if (is_string($value)) {
            $value = ltrim($value, '/');
            // key/value      -> array('key' => 'value');
            // key/value/key2 -> array('key => 'value', 'key2' => null);
            $params = explode('/', $value);
            if (is_array($params)) {
                $i = 1;
                foreach ($params as $key => $val) {
                    if (($key % 2) === 0) {
                        $requests[$val] = isset($params[$i]) ? $params[$i] : null;
                    }
                    $i ++;
                }
            }
        }

        if (isset($_GET) && count($_GET) > 0) {
            $requests = array_merge($requests, $_GET);
        }

        if (isset($_POST) && count($_POST) > 0) {
            $requests = array_merge($requests, $_POST);
        }

        return (is_array($requests) && count($requests) > 0) ? $requests : null;
    }

    /**
     * Delete null byte
     *
     * @param  mixed $value String values
     * @access public
     * @return mixed Strings which delete null byte
     */
    public static function deleteNullByte($value)
    {
        if (is_array($value)) {
            return array_map('Net_URL_Dispatcher::deleteNullByte', $value);
        }
        return str_replace(pack('c', 0), '', $value);
    }

    /**
     * Ensure that filename does not contain exploits
     *
     * @param  mixed $filename
     * @access public
     * @throws Net_URL_Dispatcher_Exception
     * @return void
     */
    public static function securityCheck($filename)
    {
        if (preg_match('/[^a-z0-9\\/\\\\_.-]/i', $filename)) {
            throw new Net_URL_Dispatcher_Exception('Illegal chatacter in filename');
        }
    }

    /**
     * Autoload class
     *
     * @param  mixed $className Class name
     * @access public
     * @return void
     */
    public static function load($className)
    {
        if (!class_exists($className, false)) {
            $path = str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';
            self::securityCheck($path);
            require_once $path;
        }
    }
}
