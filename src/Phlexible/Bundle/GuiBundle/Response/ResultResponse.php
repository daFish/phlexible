<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\GuiBundle\Response;

use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * ExtJS result response
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @author Phillip Look <pl@brainbits.net>
 */
class ResultResponse extends JsonResponse
{
    const RESULT_SUCCESS = true;
    const RESULT_FAILURE = false;

    /**
     * Generate a standard result
     *
     * @param bool  $result     True for success, false for failure
     * @param array $message    (Optional) Message
     * @param array $data       (Optional) Data
     * @param array $additional (Optional) Additional values
     */
    public function __construct($result = self::RESULT_SUCCESS,
                                $message = null,
                                array $data = array(),
                                array $additional = array())
    {
        parent::__construct();

        $this->setResult($result, $message, $data, $additional);
    }

    /**
     * Generate a standard result
     *
     * @param bool  $result     True for success, false for failure
     * @param array $message    (Optional) Message
     * @param array $data       (Optional) Data
     * @param array $additional (Optional) Additional values
     */
    public function setResult($result = self::RESULT_SUCCESS,
                              $message = null,
                              array $data = array(),
                              array $additional = array())
    {
        $this->headers->set('X-Phlexible-Response', 'result');

        $values = array(
            'success' => $result,
            'msg'     => $message,
            'data'    => $data,
        );

        if (is_array($additional) && count($additional)) {
            foreach ($additional as $key => $value) {
                $values += $additional;
            }
        }

        $this->setData($values);
    }
}