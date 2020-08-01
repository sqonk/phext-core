<?php
declare(strict_types=1);


use PHPUnit\Framework\TestCase;
use sqonk\phext\core\numbers;

class NumbersTest extends TestCase
{
    public function testConstrainToMin()
    {
        $this->assertSame(numbers::constrain(5, 6, 10), 6);
    }
    
    public function testConstrainToMax()
    {
        $this->assertSame(numbers::constrain(10.1, 6, 10), 10);
    }
    
    public function testNoConstrain()
    {
        $this->assertSame(numbers::constrain(7, 6, 10), 7);
    }
    
    public function testIsWithinFailsMin()
    {
        $this->assertSame(false, numbers::is_within(4, 5, 10));
    }
    
    public function testIsWithinFailsMax()
    {
        $this->assertSame(false, numbers::is_within(11, 5, 10));
    }
    
    public function testIsWithinPasses()
    {
        $this->assertSame(true, numbers::is_within(7, 5, 10));
    }
}