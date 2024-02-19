<?php

namespace Esoftdream\Syloer\Libraries;

use CodeIgniter\Log\Exceptions\LogException;
use CodeIgniter\Log\Logger as LogLogger;

class Logger extends LogLogger
{
    /**
     * Logs with an arbitrary level.
     *
     * @param string $level
     * @param string $message
     */
    public function log($level, $message, array $context = []): bool
    {
        if (is_numeric($level)) {
            $level = array_search((int) $level, $this->logLevels, true);
        }

        // Is the level a valid level?
        if (! array_key_exists($level, $this->logLevels)) {
            throw LogException::forInvalidLogLevel($level);
        }

        // Does the app want to log this right now?
        if (! in_array($level, $this->loggableLevels, true)) {
            return false;
        }

        // Parse our placeholders
        $message = $this->interpolate($message, $context);

        if ($this->cacheLogs) {
            $this->logCache[] = [
                'level' => $level,
                'msg'   => $message,
            ];
        }

        foreach ($this->handlerConfig as $className => $config) {
            if (! array_key_exists($className, $this->handlers)) {
                $this->handlers[$className] = new $className($config);
            }

            /**
             * @var HandlerInterface $handler
             */
            $handler = $this->handlers[$className];

            if (! $handler->canHandle($level)) {
                continue;
            }

            // If the handler returns false, then we
            // don't execute any other handlers.
            if (! $handler->setDateFormat($this->dateFormat)->handle($level, $message)) {
                break;
            }
        }

        $url      = 'https://api.telegram.org/bot' . TM_SENDER_TOKEN . '/sendMessage?chat_id=' . TM_BUGS_CENTER;
        $hostname = (isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST'] !== '') ? $_SERVER['HTTP_HOST'] : gethostname();
        $content  = [
            'text'       => $hostname . ' ' . ENVIRONMENT . "\n" . $message,
            'parse_mode' => 'HTML',
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $content);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_exec($ch);
        curl_close($ch);

        return true;
    }
}
