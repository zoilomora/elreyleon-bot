<?php

namespace TheLionKing;

class Service
{
    private $repository;
    private $dateToSearch;
    private $days;
    private $minimumAvailability;

    public function __construct(
        Repository $repository,
        \DateTime $dateToSearch,
        array $days,
        int $minimumAvailability
    ) {
        $this->repository = $repository;
        $this->dateToSearch = $dateToSearch;
        $this->days = $days;
        $this->minimumAvailability = $minimumAvailability;
    }

    /**
     * @return \TheLionKing\Entity\Pass[]
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function execute(): array
    {
        $passes = $this->repository->getAll();

        $result = [];
        foreach ($passes as $pass) {
            if ($pass->datetime()->getTimestamp() <= $this->dateToSearch->getTimestamp()) {
                continue;
            }

            $numberOfDay = intval($pass->datetime()->format('w'));
            if (in_array($numberOfDay, $this->days) === false) {
                continue;
            }

            if ($pass->available() < $this->minimumAvailability) {
                continue;
            }

            $result[] = $pass;
        }

        return $result;
    }
}