<?php

/**
 * Xi
 *
 * @category Xi
 * @package  Doctrine
 * @license  http://www.opensource.org/licenses/BSD-3-Clause New BSD License
 */

namespace Xi\Doctrine\DBAL\Logging;

use Doctrine\DBAL\Logging\SQLLogger,
    Zend_Wildfire_Plugin_FirePhp as FirePhp,
    Zend_Wildfire_Plugin_FirePhp_TableMessage as TableMessage;

/**
 * Doctrine query logger for Zend FirePHP
 *
 * @category Xi
 * @package  Doctrine
 * @author   Mikko Hirvonen <mikko.petteri.hirvonen@gmail.com>
 */
class ZendFirePhpLogger implements SQLLogger
{
    /**
     * Label of the table message
     *
     * @var string
     */
    private $label;

    /**
     * @var TableMessage
     */
    private $message;

    /**
     * Current query start time
     *
     * @var float
     */
    private $currentStartTime;

    /**
     * Current query SQL
     *
     * @var string
     */
    private $currentSql;

    /**
     * Current query parameters
     *
     * @var array
     */
    private $currentParams = array();

    /**
     * Total elapsed time
     *
     * @var float
     */
    private $totalTime = 0.0;

    /**
     * Total number of queries executed
     *
     * @var integer
     */
    private $totalQueries = 0;

    /**
     * @param  string            $label
     * @return ZendFirePhpLogger
     */
    public function __construct($label = 'Doctrine SQL queries')
    {
        $this->label = $label;

        $this->message = new TableMessage($label);
        $this->message->setBuffered(true);
        $this->message->setHeader(array('Time', 'Query', 'Parameters'));
        $this->message->setDestroy(true);

        FirePhp::getInstance()->send($this->message);
    }

    /**
     * Implementation of SQLLogger::startQuery
     *
     * @param  string $sql    The SQL to be executed
     * @param  array  $params The SQL parameters
     * @param  array  $types
     * @return void
     */
    public function startQuery($sql, array $params = null, array $types = null)
    {
        $this->currentStartTime = \microtime(true);
        $this->currentSql       = $sql;
        $this->currentParams    = $params;
    }

    /**
     * Implementation of SQLLogger::stopQuery
     *
     * @return void
     */
    public function stopQuery()
    {
        $this->message->setDestroy(false);

        $time = \microtime(true) - $this->currentStartTime;

        $this->totalTime += $time;
        $this->totalQueries++;

        // Add a row to message
        $this->message->addRow(array(
            number_format($time, 5),
            $this->currentSql,
            $this->currentParams
        ));

        // Update message label
        $this->message->setLabel(
            sprintf(
                '%s (%d @ %f sec)',
                $this->label,
                $this->totalQueries,
                number_format($this->totalTime, 5)
            )
        );
    }
}
