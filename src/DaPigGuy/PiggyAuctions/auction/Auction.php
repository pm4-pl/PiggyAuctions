<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyAuctions\auction;

use DaPigGuy\PiggyAuctions\PiggyAuctions;
use pocketmine\item\Item;
use pocketmine\Player;

/**
 * Class Auction
 * @package DaPigGuy\PiggyAuctions\auction
 */
class Auction
{
    /** @var int */
    public $id;

    /** @var string */
    public $auctioneer;
    /** @var Item */
    public $item;
    /** @var int */
    public $startDate;
    /** @var int */
    public $endDate;
    /** @var bool */
    public $claimed;
    /** @var array|AuctionBid[] */
    public $claimedBids;
    /** @var array|AuctionBid[] */
    public $bids;

    /**
     * Auction constructor.
     * @param int $id
     * @param string $auctioneer
     * @param Item $item
     * @param int $startDate
     * @param int $endDate
     * @param bool $claimed
     * @param array $claimedBids
     * @param AuctionBid[] $bids
     */
    public function __construct(int $id, string $auctioneer, Item $item, int $startDate, int $endDate, bool $claimed, array $claimedBids, array $bids)
    {
        $this->id = $id;
        $this->auctioneer = $auctioneer;
        $this->item = $item;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->claimed = $claimed;
        $this->claimedBids = $claimedBids;
        $this->bids = $bids;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getAuctioneer(): string
    {
        return $this->auctioneer;
    }

    /**
     * @return Item
     */
    public function getItem(): Item
    {
        return $this->item;
    }

    /**
     * @return int
     */
    public function getStartDate(): int
    {
        return $this->startDate;
    }

    /**
     * @return int
     */
    public function getEndDate(): int
    {
        return $this->endDate;
    }

    /**
     * @return bool
     */
    public function hasExpired(): bool
    {
        $expired = time() > $this->endDate;
        if ($expired && count($this->getClaimedBids()) === count($this->getBids())) {
            PiggyAuctions::getInstance()->getAuctionManager()->removeAuction($this);
            return true;
        }
        return $expired;
    }

    /**
     * @return bool
     */
    public function isClaimed(): bool
    {
        return $this->claimed;
    }

    /**
     * @return AuctionBid[]
     */
    public function getClaimedBids(): array
    {
        return $this->claimedBids;
    }

    /**
     * @return AuctionBid[]
     */
    public function getUnclaimedBids(): array
    {
        return array_filter($this->bids, function (AuctionBid $bid): bool {
            return !in_array($bid, $this->claimedBids);
        });
    }

    /**
     * @param string $player
     * @return AuctionBid[]
     */
    public function getUnclaimedBidsHeldBy(string $player): array
    {
        return array_filter($this->getUnclaimedBids(), function (AuctionBid $bid) use ($player): bool {
            return $bid->getBidder() === $player;
        });
    }

    /**
     * @param Player $player
     */
    public function bidderClaim(Player $player): void
    {
        $highestBidValue = 0;
        $bids = $this->getUnclaimedBidsHeldBy($player->getName());
        foreach ($bids as $bid) {
            $this->claimedBids[] = $bid;
            if ($bid->getBidAmount() > $highestBidValue) {
                $highestBidValue = $bid->getBidAmount();
            }
        }
        PiggyAuctions::getInstance()->getAuctionManager()->updateAuction($this);
        if (in_array($this->getTopBid(), $bids)) {
            $player->getInventory()->addItem($this->getItem());
            return;
        }
        //TODO: Give money to player
    }

    /**
     * @param Player $player
     */
    public function claim(Player $player): void
    {
        //TODO: Give money to auctioneer
    }

    /**
     * @return AuctionBid[]
     */
    public function getBids(): array
    {
        return $this->bids;
    }

    /**
     * @return AuctionBid|null
     */
    public function getTopBid(): ?AuctionBid
    {
        $highestBid = null;
        $highestBidAmount = 0;
        foreach ($this->bids as $bid) {
            if ($bid->getBidAmount() > $highestBidAmount) {
                $highestBid = $bid;
                $highestBidAmount = $bid->getBidAmount();
            }
        }
        return $highestBid;
    }
}