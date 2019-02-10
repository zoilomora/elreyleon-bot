<?php

namespace TheLionKing\Entity;

class Pass
{
    private $datetime;
    private $url;
    private $available;

    private function __construct(
        \DateTime $datetime,
        string $url,
        int $available
    ) {
        $this->datetime = $datetime;
        $this->url = $url;
        $this->available = $available;
    }

    public static function from(\DateTime $datetime, string $url, int $available): self
    {
        return new self($datetime, $url, $available);
    }

    public function datetime(): \DateTime
    {
        return $this->datetime;
    }

    public function url(): string
    {
        return $this->url;
    }

    public function available(): int
    {
        return $this->available;
    }
}
