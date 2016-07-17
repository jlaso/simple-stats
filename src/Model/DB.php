<?php

namespace JLaso\SimpleStats\Model;

class DB
{
    /** @var  \SQLite3 */
    protected $conn;

    /**
     * @param \SQLite3 $conn
     */
    public function __construct(\SQLite3 $conn)
    {
        $this->conn = $conn;
    }

    /**
     * @param StatsModel $model
     * @param array $range
     * @return array|null
     */
    public function fetchDataInRange(StatsModel $model, $range)
    {
        list($start, $end) = $range;
        $statement = $this->conn->prepare(sprintf(
            'SELECT `date`,SUM(`count`) AS `count` FROM `%s` WHERE `date` >= :start AND `date` <= :end GROUP BY `date` ORDER BY `date` ASC;',
            $model->getEvent()
        ));

        $statement->bindValue(':start', $start, SQLITE3_INTEGER);
        $statement->bindValue(':end', $end, SQLITE3_INTEGER);

        $rows = array();
        $result = $statement->execute();
        while($rows[] = $result->fetchArray(SQLITE3_ASSOC)){
        }

        return $rows;
    }

    /**
     * @param StatsModel $model
     * @param string $data
     * @return int
     */
    public function fetchCountByData(StatsModel $model, $data)
    {
        $statement = $this->conn->prepare(sprintf(
            "SELECT SUM(`count`) AS `count` FROM `%s` WHERE `data` = :data GROUP BY `data`;",
            $model->getEvent()
        ));

        $statement->bindValue(':data', $data, SQLITE3_TEXT);
        
        $result = $statement->execute();
        if (false === $result){
            return 0;
        }
        $row = $result->fetchArray(SQLITE3_ASSOC);

        return $row['count'];
    }

    /**
     * @param StatsModel $model
     * @param string $data
     * @param int $count
     * @return \SQLite3Result
     */
    public function insertData(StatsModel $model, $data, $count = 1)
    {
        $statement = $this->conn->prepare(sprintf(
            "INSERT INTO `%s` (`date`, `count`, `data`) VALUES (%d, :count, :data);",
            $model->getEvent(),
            intval(date('U'))
        ));

        $statement->bindValue(':data', $data, SQLITE3_TEXT);
        $statement->bindValue(':count', $count, SQLITE3_INTEGER);

        return $statement->execute();
    }
}