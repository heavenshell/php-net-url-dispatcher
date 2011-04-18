<?php
/**
 * Abstract class for controller | action class
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
 * Net_URL_Dispatcher_Abstract
 *
 * @category  Net
 * @package   Net_URL_Dispatcher
 * @version   $id$
 * @copyright 2009-2010 Shinya Ohyanagi
 * @author    Shinya Ohyanagi <sohyanagi@gmail.com>
 * @license   New BSD License
 */
abstract class Net_URL_Dispatcher_Abstract
{
    /**
     * Parameters
     *
     * @var    mixed
     * @access protected
     */
    protected $_params = null;

    /**
     * Actions
     *
     * @var    mixed
     * @access private
     */
    private $_actions = null;

    /**
     * Constructor
     *
     * @access public
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Stack action
     *
     * @param  mixed $action Action name
     * @param  mixed $controller Controller name
     * @param  mixed $params Parameters
     * @access public
     * @return Net_URL_Dispatcher_Abstract Fluent interface
     */
    public function actionStack($action, $controller = null, $params = null)
    {
        $this->_actions[] = array(
            'action'     => $action,
            'controller' => $controller,
            'params'     => $params
        );

        return $this;
    }

    /**
     * Get action
     *
     * @access public
     * @return array or null
     */
    public function getActionStack()
    {
        return is_array($this->_actions) ? array_shift($this->_actions) : null;
    }

    /**
     * Set parameters
     *
     * @param  mixed $value
     * @access public
     * @return Net_URL_Dispatcher_Abstract Fluent interface
     */
    public function setParams($value)
    {
        $this->_params = $value;
        return $this;
    }

    /**
     * Get parameters
     *
     * @access public
     * @return array All parameters
     */
    public function getParams()
    {
        return $this->_params;
    }

    /**
     * Set parameter
     *
     * @param  mixed $key Key name
     * @param  mixed $value Value
     * @access public
     * @return Net_URL_Dispatcher_Abstract Fluent interface
     */
    public function setParam($key, $value)
    {
        $this->_params[$key] = $value;
        return $this;
    }

    /**
     * Get parameter
     *
     * @param  mixed $key Parameter key name
     * @param  mixed $default Default value
     * @access public
     * @return mixed Parameter
     */
    public function getParam($key, $default = null)
    {
        return isset($this->_params[$key]) ? $this->_params[$key] : $default;
    }
}
