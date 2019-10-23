-- #! mysql
-- #{ piggyauctions

-- # { init
CREATE TABLE IF NOT EXISTS auctions
(
    id           INTEGER PRIMARY KEY AUTO_INCREMENT,
    auctioneer   VARCHAR(15),
    item         JSON,
    startdate    INTEGER,
    enddate      INTEGER,
    claimed      INTEGER,
    claimed_bids JSON,
    bids         JSON
);
-- # }

-- # { load
SELECT *
FROM auctions;
-- # }

-- # { add
-- #    :auctioneer string
-- #    :item string
-- #    :startdate int
-- #    :enddate int
-- #    :claimed int
-- #    :claimed_bids string
-- #    :bids string
INSERT INTO auctions (auctioneer, item, startdate, enddate, claimed, claimed_bids, bids)
VALUES (:auctioneer, :item, :startdate, :enddate, :claimed, :claimed_bids, :bids);
-- # }

-- # { update
-- #    :id int
-- #    :claimed int
-- #    :claimed_bods string
-- #    :bids string
UPDATE auctions
SET claimed      = :claimed,
    claimed_bids = :claimed_bids,
    bids         = :bids
WHERE id = :id;
-- # }

-- # { remove
-- #    :id int
DELETE
FROM auctions
WHERE id = :id;
-- # }

-- #}