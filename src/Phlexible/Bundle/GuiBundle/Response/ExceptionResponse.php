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
 * Exception response.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @author Phillip Look <pl@brainbits.net>
 */
class ExceptionResponse extends JsonResponse
{
    /**
     * @var string
     */
    private $rootDir;

    /**
     * @param \Exception $exception
     * @param string     $rootDir
     */
    public function __construct(\Exception $exception, $rootDir = '/Users/swentz/Sites/phlexible-tipfinder/tipfinder-extjs5/')
    {
        parent::__construct();

        $this->rootDir = $rootDir;
        $this->setException($exception);
    }

    /**
     * Set exception data.
     *
     * @param \Exception $exception
     */
    public function setException(\Exception $exception)
    {
        $this->headers->set('X-Phlexible-Response', 'exception');
        $this->setStatusCode(500);
        $this->setData($this->getExceptionData($exception));
    }

    /**
     * @param \Exception $exception
     *
     * @return array
     */
    private function getExceptionData(\Exception $exception)
    {
        $data = array(
            'classname' => get_class($exception),
            'message' => $exception->getMessage(),
            'code' => $exception->getCode(),
            'trace' => $exception->getTrace(),
            'traceAsString' => $exception->getTraceAsString(),
        );

        $trace = $data['trace'][0];
        if (isset($trace['file']) && isset($trace['line']) && is_readable($trace['file'])) {
            $excerpt = $this->getExcerpt($trace['file'], $trace['line']);
            if ($excerpt) {
                $data['trace'][0]['excerpt'] = $excerpt;
            }
        }

        $len = mb_strlen($this->rootDir);
        foreach ($data['trace'] as $index => $trace) {
            if (isset($trace['file'])) {
                $data['trace'][$index]['file'] = mb_substr($trace['file'], $len);
            }
        }

        $previousException = $exception->getPrevious();
        if ($previousException) {
            $data['previous'] = $this->getExceptionData($previousException);
        }

        return $data;
    }

    /**
     * @param string $file
     * @param int    $line
     *
     * @return string
     */
    private function getExcerpt($file, $line)
    {
        // highlight_file could throw warnings
        // see https://bugs.php.net/bug.php?id=25725
        $code = @highlight_file($file, true);
        // remove main code/span tags
        $code = preg_replace('#^<code.*?>\s*<span.*?>(.*)</span>\s*</code>#s', '\\1', $code);
        $content = preg_split('#<br />#', $code);

        $lines = array();
        for ($i = max($line - 3, 1), $max = min($line + 3, count($content)); $i <= $max; ++$i) {
            $lines[] = '<li'.($i === $line ? ' class="selected"' : '').'>'.
                '<code>'.$this->fixCodeMarkup($content[$i - 1]).'</code></li>';
        }

        return '<ol start="'.max($line - 3, 1).'">'.implode("\n", $lines).'</ol>';
    }

    /**
     * @param string $line
     *
     * @return string
     */
    protected function fixCodeMarkup($line)
    {
        // </span> ending tag from previous line
        $opening = strpos($line, '<span');
        $closing = strpos($line, '</span>');
        if (false !== $closing && (false === $opening || $closing < $opening)) {
            $line = substr_replace($line, '', $closing, 7);
        }

        // missing </span> tag at the end of line
        $opening = strpos($line, '<span');
        $closing = strpos($line, '</span>');
        if (false !== $opening && (false === $closing || $closing > $opening)) {
            $line .= '</span>';
        }

        return $line;
    }
}
