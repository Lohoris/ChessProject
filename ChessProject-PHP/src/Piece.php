<?php

namespace SolarWinds\Chess;

abstract class Piece
{
    const INVALID = -1;
    
    /** @var ChessBoard */
    protected $chessBoard;
    
    /** @var int */
    protected $xCoordinate;
    
    /** @var int */
    protected $yCoordinate;
    
    private $_isWhite;
    
    private $_isCaptured;
    
    public function __construct (bool $isWhite) {
        $this->xCoordinate = static::INVALID;
        $this->yCoordinate = static::INVALID;
        $this->_isWhite = $isWhite;
        $this->_isCaptured = FALSE;
    }
    
    public function initialise (ChessBoard $chessBoard, int $xCoordinate, int $yCoordinate) {
        $this->chessBoard = $chessBoard;
        $this->xCoordinate = $xCoordinate;
        $this->yCoordinate = $yCoordinate;
    }
    
    /** @return int */
    public function getX () {
        return $this->xCoordinate;
    }
    
    /** @return int */
    public function getY () {
        return $this->yCoordinate;
    }
    
    public function isWhite () {
        return $this->_isWhite;
    }
    public function isBlack () {
        return !$this->_isWhite;
    }
    public function colourName () {
        if ( $this->isWhite() )
            return 'white';
        if ( $this->isBlack() )
            return 'black';
        throw new \InvalidArgumentException("Unknown colour for [$this].");
    }
    public function isActive () {
        if ( $this->_isCaptured )
            return FALSE;
        
        return TRUE;
    }
    
    /**
     * Returns TRUE if $check is of the same colour of $this.
     */
    public function isFriendly (Piece $check) {
        return $check->colourName()===$this->colourName();
    }
    
    /**
     * This function must call validMove().
     */
    public function move (int $newX, int $newY) {
        $former_x = $this->xCoordinate;
        $former_y = $this->yCoordinate;
        
        $dest_cell = $this->validMove($newX,$newY);
        
        $this->xCoordinate = $newX;
        $this->yCoordinate = $newY;
        
        $this->chessBoard->handleMove($this,$dest_cell,$former_x,$former_y);
        
        // TODO: promotions
    }
    
    /**
     * Throws InvalidMoveException if the move is invalid.
     * Returns TRUE if the move is valid and the destination cell is empty.
     * Returns the captured Piece otherwise.
     */
    public function validMove (int $newX, int $newY) {
        if ( !$this->isActive() )
            throw new InvalidMoveException("$this: Inactive pieces can't move.");
        
        if ( !$this->chessBoard->isLegalBoardPosition($newX,$newY) )
            throw new InvalidMoveException("$this: Illegal board position [$newX, $newY].");
        
        if ( !$this->validPattern($newX, $newY) )
            throw new InvalidMoveException("$this: Illegal moving pattern [$newX, $newY].");
        
        if ( !$this->validPath($newX, $newY) )
            throw new InvalidMoveException("$this: Illegal moving path [$newX, $newY].");
        
        $dest_cell = $this->chessBoard->getCell($newX,$newY);
        if ( $dest_cell === ChessBoard::EMPTY ) {
            // The destination cell is empty
            
            return TRUE;
        }
        else {
            // The Piece in the destination cell must be of a different colour
            
            if ( $dest_cell->isFriendly($this) )
                throw new InvalidMoveException("$this: Can't capture your own piece [$newX, $newY].");
            
            return $dest_cell; // Returning the to‑be‑captured Piece
        }
        
        throw new \LogicException("Should never end up here.");
    }
    
    /**
     * This only checks if the moving pattern is correct.
     * That is: it doesn't check if the cell exists, is empty, there's no pieces blocking the path, etc.
     * It only and solely checks the pattern (i.e. the direction, the distance, etc.)
     */
    abstract protected function validPattern (int $newX, int $newY);
    
    /**
     * This checks if the path that the Piece has to walk is clear.
     * Pieces that move by a single square (King), or that ignore the path (Knight) will just return TRUE.
     * This DOES NOT check the destination cell, only the path towards it!
     */
    abstract protected function validPath (int $newX, int $newY);
    
    public function capturedBy (Piece $by) {
        // NOTE: while $by isn't currently used, we might easily need it for displaying, or for logging.
        
        $this->_isCaptured = TRUE;
        $this->xCoordinate = static::INVALID;
        $this->yCoordinate = static::INVALID;
    }
    
    public function __toString () {
        return get_class($this)." ".($this->isWhite()?'white':'black')." @({$this->xCoordinate}, {$this->yCoordinate})";
    }
}
