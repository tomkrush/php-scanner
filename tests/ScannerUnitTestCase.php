<?php

class ScannerUnitTestCase extends UnitTestCase
{
	public function test_scan()
	{
		$scanner = new Scanner('This is a string');
		
		$this->assertEquals('0', $scanner->pos(), 'Position is 0');
		$this->assertEquals('This', $scanner->scan('/\w+/'), 'Token is a word');
		$this->assertEquals(NULL, $scanner->scan('/\w+/'), 'Token is not a word');
		$this->assertEquals(' ', $scanner->scan('/ /'), 'Token is a space');
		$this->assertEquals('is', $scanner->scan('/\w+/'), 'Token is a word');
		$this->assertFalse($scanner->eos(), 'Not end of string');
		$this->assertEquals(' ', $scanner->scan('/ /'), 'Token is a space');
		$this->assertEquals('a', $scanner->scan('/\w+/'), 'Token is a word');
		$this->assertEquals(' ', $scanner->scan('/ /'), 'Token is a space');
		$this->assertEquals('string', $scanner->scan('/\w+/'), 'FToken is a word');
		$this->assertTrue($scanner->eos(), 'End of string');
		$this->assertEquals('16', $scanner->pos(), 'Position is 16');
		
		$this->assertEquals('0', $scanner->reset(), 'Position is 0 again');
		$this->assertEquals('This', $scanner->scan('/\w+/'), 'Token is a word');

		$this->assertEquals(' is a string', $scanner->rest(), 'Rest of string returned');
		
		$this->assertEquals(' is a string', $scanner->peek(), 'Peek returned rest of string');
		$this->assertEquals(' i', $scanner->peek(2), 'Peek returned part of string');
		$this->assertEquals(10, $scanner->exists('/string/'), 'Regex does exist in later string');
		
		$this->assertEquals('0', $scanner->reset(), 'Position is 0 again');

		$this->assertEquals('T', $scanner->getch(), 'Returns next character and adjusts pointer');

		$this->assertEquals('is', $scanner->scan_until('/is/'), 'Scan until find word');
		$this->assertEquals('1', $scanner->skip('/ /'), 'Skip space');
		$this->assertEquals('11', $scanner->skip_until('/string/'), 'Skips to string and returns length');

		$this->assertEquals('5', $scanner->unscan(), 'Goes back to previous pointer');

	}
}