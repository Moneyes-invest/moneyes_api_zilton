<?php


namespace App\Message;

use DateTime;

class FetchBinanceTransactions
{

    private string $accountId;
    private ?DateTime $previousUpdate;

    public function __construct(string $accountId, ?DateTime $previousUpdate = null)
    {
        $this->accountId = $accountId;
        $this->previousUpdate = $previousUpdate;
    }

    /**
     * @return string
     */
    public function getAccountId(): string
    {
        return $this->accountId;
    }

    /**
     * @return DateTime|null
     */
    public function getPreviousUpdate(): ?DateTime
    {
        return $this->previousUpdate;
    }


}