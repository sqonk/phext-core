<?php
declare(strict_types=1);


use PHPUnit\Framework\TestCase;

class GlobalFuncsTest extends TestCase
{
    public function testSequence()
    {
        $expected = range(0, 10);
        foreach (sequence(10) as $i)
            $this->assertSame($i, array_shift($expected));
        
        $expected = range(1, 10);
        foreach (sequence(1, 5) as $i)
            $this->assertSame($i, array_shift($expected));
        
        $expected = [2,4,6,8,10];
        foreach (sequence(2, 10, 2) as $i)
            $this->assertSame($i, array_shift($expected));
    }
    
    public function testObjectify()
    {
        $o = objectify(['x' => 1, 'y' => 2]);
        $this->assertIsObject($o);
        
        $this->assertSame(1, $o->x);
        $this->assertSame(2, $o->y);
    }
    
    public function testNamedObjectify()
    {
        $c = named_objectify(['x', 'y']);
        $this->assertIsCallable($c);
        
        $o = $c(1,2);
        $this->assertIsObject($o);
        $this->assertSame(1, $o->x);
        $this->assertSame(2, $o->y);
    }
    
    public function testVarIsStringable()
    {
        $ob = new class() {
            public function __toString() {
                return 'test';
            }
        };
        $ob2 = new class() {};
        
        $this->assertSame(true, var_is_stringable('abc'));
        $this->assertSame(true, var_is_stringable(2));
        $this->assertSame(true, var_is_stringable($ob));
        $this->assertSame(false, var_is_stringable($ob2));
    }
}