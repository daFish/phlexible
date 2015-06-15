<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\GuiBundle\Response;

use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * ExtJS form response
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @author Phillip Look <pl@brainbits.net>
 */
class FormResponse extends JsonResponse
{
    /**
     * @var boolean
     */
    private $success = true;

    /**
     * @var array
     */
    private $errors = array();

    /**
     * @var array
     */
    private $additional = array();

    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->applyResult();
    }

    /**
     * Add error
     *
     * @param string $key
     * @param string $message
     */
    public function addError($key, $message)
    {
        $this->success = false;
        $this->errors[$key] = $message;

        $this->applyResult();
    }

    /**
     * Has errors?
     *
     * @return boolean
     */
    public function hasErrors()
    {
        return count($this->errors) > 0;
    }

    /**
     * Add additional value
     *
     * @param string $key
     * @param string $value
     */
    public function addValue($key, $value)
    {
        $this->additional[$key] = $value;

        $this->applyResult();
    }

    /**
     * Generate a standard form result
     */
    private function applyResult()
    {
        $this->headers->set('X-Phlexible-Response', 'form');

        $values = array(
            'success' => $this->success,
            'errors'  => $this->errors,
        );

        if (count($this->additional)) {
            $values += $this->additional;
        }

        $this->setData($values);
    }
}
