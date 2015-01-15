<?php
/**
 * phlexible
 *
 * @copyright 2007 brainbits GmbH (http://www.brainbits.net)
 * @license   http://www.makeweb.de/license/new-bsd     New BSD License
 */

namespace Phlexible\Bundle\GuiBundle\Response;

use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Exception response
 *
 * @author  Stephan Wentz <sw@brainbits.net>
 * @author  Phillip Look <pl@brainbits.net>
 */
class ExceptionResponse extends JsonResponse
{
    /**
     * @param \Exception $exception
     */
    public function __construct(\Exception $exception)
    {
        parent::__construct();

        $this->setException($exception);
    }

    /**
     * Set exception data
     *
     * @param \Exception $exception
     */
    public function setException(\Exception $exception)
    {
        $this->headers->set('X-Phlexible-Response', 'exception');
        $this->setStatusCode(500);
        $this->setData($this->getExceptionData($exception));
    }

    private function getExceptionData(\Exception $exception)
    {
        $data = array(
            'classname'  => get_class($exception),
            'message'    => $exception->getMessage(),
            'code'       => $exception->getCode(),
            'stacktrace' => $exception->getTraceAsString(),
        );

        $previousException = $exception->getPrevious();
        if ($previousException) {
            $data['previous'] = $this->getExceptionData($previousException);
        }

        return $data;
    }
}
