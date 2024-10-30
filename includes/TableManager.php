<?php

namespace Krut1LoginAttemptsLimitWp;

/**
 * Class TableManager
 *
 * @package Krut1LoginAttemptsLimitWp
 */
abstract class TableManager
{
    private static $instances = [];

    public $increased = false;

    /**
     * @return self
     */
    final public static function getInstance(): self
    {
        $calledClass = get_called_class();

        if (!isset(self::$instances[$calledClass])) {
            self::$instances[$calledClass] = new $calledClass();
        }

        return self::$instances[$calledClass];
    }

    public $tableName = '';

    /**
     * Get table name
     *
     * @return string
     */
    public function getTableName(): string
    {
        global $wpdb;

        return $wpdb->prefix . $this->tableName;
    }

    /**
     * Get row for current IP
     *
     * @return array|null
     */
    public function getRowByIp(): ?array
    {
        global $wpdb;

        // Disable W3TC caching if not already done!
        if (!defined('DONOTCACHEDB')) {
            define('DONOTCACHEDB', true);
        }

        $ip = ip2long(Ip::instance()->getIp());
        $tableName = $this->getTableName();

        return $wpdb->get_row("SELECT * FROM `{$tableName}` WHERE `ip` = $ip", ARRAY_A);
    }

    /**
     * Get most popular rows
     *
     * @param int $limit
     * @return array
     */
    public function getMostPopularRows($limit = 10): array
    {
        global $wpdb;

        $tableName = $this->getTableName();

        return $wpdb->get_results("SELECT * FROM `{$tableName}` ORDER BY `total_attempts` DESC LIMIT {$limit}", ARRAY_A);
    }

    /**
     * Clear table
     *
     * @return bool
     */
    public function clearTable(): bool
    {
        global $wpdb;

        $tableName = $this->getTableName();

        if ($wpdb->query("DELETE FROM {$tableName}")) {
            return true;
        }

        return false;
    }

    /**
     * Set attempts to 0
     *
     * @param $id
     * @throws \Exception
     */
    public function resetAttempts($id): void
    {
        global $wpdb;

        $wpdb->update($this->getTableName(), ['attempts' => 0, 'last_time' => (new \DateTime())->format(DATE_ATOM)], ['id' => $id]);
    }

    /**
     * Increase and save attempt
     *
     * @param array $row
     * @param bool  $blocked
     * @throws \Exception
     */
    public function increaseAttempts(array $row, $blocked = false): void
    {
        global $wpdb;

        // If already increased (because after blocking happens login failed)
        if ($this->increased) {
            return;
        }

        $updateArray = [
            'attempts' => $row['attempts'] + 1,
            'total_attempts' => $row['total_attempts'] + 1
        ];

        if ($blocked) {
            $updateArray['blocked_attempts'] = $row['blocked_attempts'] + 1;
        } else {
            $updateArray['last_time'] = (new \DateTime())->format(DATE_ATOM);
        }

        $wpdb->update($this->getTableName(), $updateArray, ['id' => $row['id']]);

        $this->increased = true;
    }

    /**
     * @param \DateTime $firstDateTime
     * @param \DateTime $secondDateTime
     * @return int
     */
    public function getMinutes(\DateTime $firstDateTime, \DateTime $secondDateTime): int
    {
        return (int)abs($firstDateTime->getTimestamp() - $secondDateTime->getTimestamp()) / 60;
    }
}
