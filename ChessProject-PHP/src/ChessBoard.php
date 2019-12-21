<?php

namespace SolarWinds\Chess;

class ChessBoard
{
    const EMPTY = -1;
    const BOARD_WIDTH = 8;
    const BOARD_HEIGHT = 8;

    private $cells;

    public function __construct()
    {
        // NOTE: BOARD_* should be static::, not self::, because they should be overridable by a child class
        $this->cells = array_fill(0, static::BOARD_WIDTH, array_fill(0, static::BOARD_HEIGHT, self::EMPTY));
    }

    public function add(Pawn $pawn, $xCoordinate, $yCoordinate)
    {
        if ( !$this->isLegalBoardPosition($xCoordinate, $yCoordinate) )
            throw new \InvalidArgumentException("Invalid board position [$xCoordinate, $yCoordinate].");
        
        if ( !$this->isCellEmpty($xCoordinate, $yCoordinate) )
            throw new \InvalidArgumentException("Cell not empty [$xCoordinate, $yCoordinate].");
        
        if ( !is_int($xCoordinate) || !is_int($yCoordinate) )
            throw new \InvalidArgumentException("Non‑integer coordinate ($xCoordinate, $yCoordinate)(".gettype($xCoordinate).", ".gettype($yCoordinate).").");
        
        $this->cells[$xCoordinate][$yCoordinate] = $pawn;
        $pawn->setXCoordinate($xCoordinate);
        $pawn->setYCoordinate($yCoordinate);
    }

    /**
 	 * @return boolean
 	 **/
    public function isLegalBoardPosition($xCoordinate, $yCoordinate, $THROW=FALSE)
    {
        // NOTE: doing this is better than checking if they are between 0 and BOARD_*, since it technically supports different board configurations, and since it actually checks if that cell really exists, which is the point
        
        $ret = isset($this->cells[$xCoordinate][$yCoordinate]);
        if ( $THROW && !$ret )
            throw new \InvalidArgumentException("Not a legal board position [$xCoordinate, $yCoordinate].");
        return $ret;
    }
    
    public function isCellEmpty ($xCoordinate, $yCoordinate) {
        $this->isLegalBoardPosition($xCoordinate, $yCoordinate, TRUE);
        return $this->cells[$xCoordinate][$yCoordinate] === self::EMPTY;
    }
    
    public function getCells () {
        return $this->cells; // NOTE: returns a copy
    }
    
    /**
     * Returns the board size, assuming the board is square shaped.
     */
    public function getSquareSize () {
        return [count($this->cells),count($this->cells[0])];
    }
    
    /**
     * @return Pawn|EMPTY
     */
    public function getCell ($xCoordinate, $yCoordinate) {
        $this->isLegalBoardPosition($xCoordinate, $yCoordinate, TRUE);
        return $this->cells[$xCoordinate][$yCoordinate];
    }
}
