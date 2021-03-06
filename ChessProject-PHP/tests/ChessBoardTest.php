<?php

namespace SolarWinds\Chess;

require_once 'vendor/autoload.php';

class ChessBoardTest extends \PHPUnit_Framework_TestCase
{
    /** @var  ChessBoard */
    private $_testSubject;
    
    public function setUp () {
        $this->_testSubject = new ChessBoard();
    }
    
    public function testHas_Correct_Width () {
        $count = $this->_testSubject->getWidth();
        $req = ChessBoard::BOARD_WIDTH;
        $this->assertEquals($req, $count, "Required width[$req] != count[$count].");
    }
    public function testHas_Correct_Height () {
        $cells = $this->_testSubject->getCells();
        $req = ChessBoard::BOARD_HEIGHT;
        for ( $col=0; $col<count($cells); $col++ ) {
            $count = count($cells[$col]);
            $this->assertEquals($req, $count, "Required height[$req] != count[$count] at column[$col].");
        }
    }
    
    public function testIsLegalBoardPosition_True_X_equals_0_Y_equals_0 () {
        $isValidPosition = $this->_testSubject->isLegalBoardPosition(0, 0);
        $this->assertTrue($isValidPosition);
    }
    public function testIsLegalBoardPosition_True_X_equals_5_Y_equals_5 () {
        $isValidPosition = $this->_testSubject->isLegalBoardPosition(5, 5);
        $this->assertTrue($isValidPosition);
    }
    public function testIsLegalBoardPosition_False_X_equals_11_Y_equals_5 () {
        $isValidPosition = $this->_testSubject->isLegalBoardPosition(11, 5);
        $this->assertFalse($isValidPosition);
    }
    public function testIsLegalBoardPosition_False_X_equals_0_Y_equals_9 () {
        $isValidPosition = $this->_testSubject->isLegalBoardPosition(0, 9);
        $this->assertFalse($isValidPosition);
    }
    public function testIsLegalBoardPosition_False_X_equals_11_Y_equals_0 () {
        $isValidPosition = $this->_testSubject->isLegalBoardPosition(11, 0);
        $this->assertFalse($isValidPosition);
    }
    public function testIsLegalBoardPosition_False_For_Negative_X_Values () {
        $isValidPosition = $this->_testSubject->isLegalBoardPosition(-1, 5);
        $this->assertFalse($isValidPosition);
    }
    public function testIsLegalBoardPosition_False_For_Negative_Y_Values () {
        $isValidPosition = $this->_testSubject->isLegalBoardPosition(5, -1);
        $this->assertFalse($isValidPosition);
    }
    
    public function testAvoids_Duplicate_Positioning () {
        $firstPawn = new Pawn(FALSE);
        $secondPawn = new Pawn(FALSE);
        $this->_testSubject->add($firstPawn, 6, 3);
        
        // NOTE: we cannot simply use expectException() here, since the test would stop right after that, ignoring what follows!
        $ex = NULL;
        try {
            $this->_testSubject->add($secondPawn, 6, 3);
        }
        catch ( \Exception $cought ) {
            $ex = $cought;
        }
        $this->assertInstanceOf('SolarWinds\Chess\InvalidMoveException',$ex);
        
        $this->assertEquals(6, $firstPawn->getX());
        $this->assertEquals(3, $firstPawn->getY());
        $this->assertEquals(Piece::INVALID, $secondPawn->getX());
        $this->assertEquals(Piece::INVALID, $secondPawn->getY());
    }
    
    public function testInvalidPositionX () {
        $firstPawn = new Pawn(FALSE);
        list($width,$height) = $this->_testSubject->getSquareSize();
        
        $this->expectException(InvalidMoveException::class);
        $this->_testSubject->add($firstPawn, $width, $height-1);
    }
    public function testInvalidPositionXY () {
        $firstPawn = new Pawn(FALSE);
        list($width,$height) = $this->_testSubject->getSquareSize();
        
        $this->expectException(InvalidMoveException::class);
        $this->_testSubject->add($firstPawn, $width, $height);
    }
    public function testInvalidPositionY () {
        $firstPawn = new Pawn(FALSE);
        list($width,$height) = $this->_testSubject->getSquareSize();
        
        $this->expectException(InvalidMoveException::class);
        $this->_testSubject->add($firstPawn, $width-1, $height);
    }
    public function testValidPosition () {
        $firstPawn = new Pawn(FALSE);
        list($width,$height) = $this->_testSubject->getSquareSize();
        
        $this->_testSubject->add($firstPawn, $width-1, $height-1);
        $gotPawn = $this->_testSubject->getCell($width-1, $height-1);
        $this->assertSame($firstPawn, $gotPawn);
    }
    
    public function testIsBoardEmpty () {
        $cells = $this->_testSubject->getCells();
        foreach ( $cells as $column ) {
            foreach ( $column as $cell ) {
                $this->assertEquals(ChessBoard::EMPTY, $cell);
            }
        }
    }
}
