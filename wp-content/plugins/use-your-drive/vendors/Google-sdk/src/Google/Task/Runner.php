<?php
/*
 * Copyright 2014 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

if (!class_exists('UYDGoogle_Client')) {
    require_once dirname(__FILE__).'/../autoload.php';
}

/**
 * A task runner with exponential backoff support.
 *
 * @see https://developers.google.com/drive/web/handle-errors#implementing_exponential_backoff
 */
class UYDGoogle_Task_Runner
{
    /**
     * @var int the max time (in seconds) to wait before a retry
     */
    private $maxDelay = 60;

    /**
     * @var int the previous delay from which the next is calculated
     */
    private $delay = 1;

    /**
     * @var int the base number for the exponential back off
     */
    private $factor = 2;

    /**
     * @var float a random number between -$jitter and will be
     *            added to $factor on each iteration to allow for a better distribution of
     *            retries
     */
    private $jitter = 0.5;

    /**
     * @var int the number of attempts that have been tried so far
     */
    private $attempts = 0;

    /**
     * @var int the max number of attempts allowed
     */
    private $maxAttempts = 1;

    /**
     * @var UYDGoogle_Client the current API client
     */
    private $client;

    /**
     * @var string the name of the current task (used for logging)
     */
    private $name;

    /**
     * @var callable the task to run and possibly retry
     */
    private $action;

    /**
     * @var array the task arguments
     */
    private $arguments;

    /**
     * Creates a new task runner with exponential backoff support.
     *
     * @param UYDGoogle_Client $client    The current API client
     * @param string           $name      The name of the current task (used for logging)
     * @param callable         $action    The task to run and possibly retry
     * @param array            $arguments The task arguments
     *
     * @throws UYDGoogle_Task_Exception when misconfigured
     */
    public function __construct(
        UYDGoogle_Client $client,
        $name,
        $action,
        array $arguments = []
    ) {
        $config = (array) $client->getClassConfig('UYDGoogle_Task_Runner');

        if (isset($config['initial_delay'])) {
            if ($config['initial_delay'] < 0) {
                throw new UYDGoogle_Task_Exception(
                    'Task configuration `initial_delay` must not be negative.'
                );
            }

            $this->delay = $config['initial_delay'];
        }

        if (isset($config['max_delay'])) {
            if ($config['max_delay'] <= 0) {
                throw new UYDGoogle_Task_Exception(
                    'Task configuration `max_delay` must be greater than 0.'
                );
            }

            $this->maxDelay = $config['max_delay'];
        }

        if (isset($config['factor'])) {
            if ($config['factor'] <= 0) {
                throw new UYDGoogle_Task_Exception(
                    'Task configuration `factor` must be greater than 0.'
                );
            }

            $this->factor = $config['factor'];
        }

        if (isset($config['jitter'])) {
            if ($config['jitter'] <= 0) {
                throw new UYDGoogle_Task_Exception(
                    'Task configuration `jitter` must be greater than 0.'
                );
            }

            $this->jitter = $config['jitter'];
        }

        if (isset($config['retries'])) {
            if ($config['retries'] < 0) {
                throw new UYDGoogle_Task_Exception(
                    'Task configuration `retries` must not be negative.'
                );
            }
            $this->maxAttempts += $config['retries'];
        }

        if (!is_callable($action)) {
            throw new UYDGoogle_Task_Exception(
                'Task argument `$action` must be a valid callable.'
            );
        }

        $this->name = $name;
        $this->client = $client;
        $this->action = $action;
        $this->arguments = $arguments;
    }

    /**
     * Checks if a retry can be attempted.
     *
     * @return bool
     */
    public function canAttmpt()
    {
        return $this->attempts < $this->maxAttempts;
    }

    /**
     * Runs the task and (if applicable) automatically retries when errors occur.
     *
     * @return mixed
     *
     * @throws UYDGoogle_Task_Retryable on failure when no retries are available
     */
    public function run()
    {
        while ($this->attempt()) {
            try {
                return call_user_func_array($this->action, $this->arguments);
            } catch (UYDGoogle_Task_Retryable $exception) {
                $allowedRetries = $exception->allowedRetries();

                if (!$this->canAttmpt() || !$allowedRetries) {
                    throw $exception;
                }

                if ($allowedRetries > 0) {
                    $this->maxAttempts = min(
                        $this->maxAttempts,
                        $this->attempts + $allowedRetries
                    );
                }
            }
        }
    }

    /**
     * Runs a task once, if possible. This is useful for bypassing the `run()`
     * loop.
     *
     * NOTE: If this is not the first attempt, this function will sleep in
     * accordance to the backoff configurations before running the task.
     *
     * @return bool
     */
    public function attempt()
    {
        if (!$this->canAttmpt()) {
            return false;
        }

        if ($this->attempts > 0) {
            $this->backOff();
        }

        ++$this->attempts;

        return true;
    }

    /**
     * Sleeps in accordance to the backoff configurations.
     */
    private function backOff()
    {
        $delay = false;

        foreach ($this->arguments as $argument) {
            if ($argument instanceof UYDGoogle_Http_Request) {
                $headers = $argument->getRequestHeaders();
                if (isset($headers['retry-after'])) {
                    $delay = (int) $headers['retry-after'] + 1;
                }
            }
        }

        if (false === $delay) {
            $delay = $this->getDelay();
        }

        $this->client->getLogger()->debug(
            'Retrying task with backoff',
            [
                'request' => $this->name,
                'retry' => $this->attempts,
                'backoff_seconds' => $delay,
            ]
        );

        usleep((int) ($delay * 1000000));
    }

    /**
     * Gets the delay (in seconds) for the current backoff period.
     *
     * @return float
     */
    private function getDelay()
    {
        $jitter = $this->getJitter();
        $factor = $this->attempts > 1 ? $this->factor + $jitter : 1 + abs($jitter);

        return $this->delay = min($this->maxDelay, $this->delay * $factor);
    }

    /**
     * Gets the current jitter (random number between -$this->jitter and
     * $this->jitter).
     *
     * @return float
     */
    private function getJitter()
    {
        return $this->jitter * 2 * wp_rand() / mt_getrandmax() - $this->jitter;
    }
}
