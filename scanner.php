<?php

function match($regex, $string, &$index)
{
	preg_match($regex, $string, $matches, PREG_OFFSET_CAPTURE);
	
	if ( isset($matches) )
	{
		if ( is_array($matches) && isset($matches[0]) && isset($matches[0][1]) )
		{
			$index = $matches[0][1];
		}
		
		return $matches;
	}
	
	return FALSE;
}

class Scanner
{
	protected $pointer 	= 0;
	protected $previousPointer = 0;
	protected $end 			= TRUE;
	protected $value 		= NULL;
	protected $match 		= NULL;
	
	public function __construct($string)
	{
		// Make sure the eos is set to false if string is present
		if ( strlen($string) > 0 )
		{
			$this->end = FALSE;
		}
		
		$this->value = $string;
	}
	
	/* Returns whether scanner has reached end of string. */
	public function eos()
	{
		return $this->end;
	}
	
	// Set or get pointer position
	public function pos()
	{
		$args = func_get_args();
		
		if ( isset($args[0]) && $index = $args[0] )
		{
			if ( $index == -1 )
			{
				$index = strlen($this->value);
			}
		
			$this->previousPointer = $this->pointer;
		
			$this->pointer = $index;
		
			if ( $this->pointer >= strlen($this->value) )
			{
				return $this->end = TRUE;
			}
		}
		
		return $this->pointer;
	}
	
	// Reset scanner pointer position to 0. Allows to scan again
	public function reset()
	{
		$this->end = FALSE;
		$this->pointer = 0;
		
		return $this->pointer;
	}
	
	// Capture the different selections from a regex pattern
	public function capture($index)
	{
		if ( is_array($this->match) && isset($this->match[$index]) ) 
		{
			print_r($this->match);
			return $this->match[$index][0];
		}

		return NULL;	
	}
	
	// Return the entire match
	public function matched()
	{
		if ( is_array($this->match) && isset($this->match[0]) ) 
		{
			return $this->match[0][0];
		}

		return NULL;
	}
	
	public function rest()
	{
		return $this->peek();
	}
	
	// Return the entire string
	public function string()
	{
		return $this->value;
	}
	
	// Look at next character without advancing the pointer
	public function peek()
	{
		$args = func_get_args();
		
		if ( isset($args[0]) && $len = $args[0] )
		{
			return substr($this->value, $this->pointer, $len);			
		}

		return substr($this->value, $this->pointer);
	}
	
	// Check to see if pattern exists in string after pointer
	public function exists($regex)
	{
		$string = $this->rest();
		$matches = match($regex, $string, $index);
		
		if ( $index >= 0 )
		{
			$this->match = $matches;
			$match = $this->matched();
			
			return $this->pointer + $index;
		}
		
		return NULL;
	}
	
	// Pull next character and advance pointer
	public function getch()
	{
		if ( ! $this->eos() ) 
		{
			$string = $this->peek(1);
			
			$this->match = array($string, $this->pointer);
			$this->pos($this->pointer + 1);
			
			return $string;
		}
		
		return NULL;
	}
	
	// Advance pointer and return match if regex matches next characters
	public function scan($regex)
	{
		$string = $this->rest();
		$matches = match($regex, $string, $index);
		
		if ( $index === 0 )
		{
			$this->match = $matches;
			$match = $this->matched();

			$this->pos($this->pointer + $index + strlen($match));
			
			return $match;
		}
		
		return NULL;
	}
	
	// Same as scan() except function scans until it reaches the pattern or eol
	public function scan_until($regex)
	{
		$string = $this->rest();
		$matches = match($regex, $string, $index);
		
		if ( $index >= 0 )
		{
			$this->match = $matches;
			$match = $this->matched();
			
			$this->pos($this->pointer + $index + strlen($match));
			
			return $match;
		}
		
		return NULL;
	}
	
	// Same as scan() except doesn't set a match
	public function skip($regex)
	{
		$this->scan($regex);
		$match = $this->matched();
		$this->match = NULL;
		
		if ( $match !== NULL )
		{
			return strlen($match);
		}
	}
	
	// Same as scan_until() but doesn't set a match
	public function skip_until($regex)
	{
		$pos = $this->pos();
		
		$this->scan_until($regex);
		$match = $this->matched();
		$this->match = NULL;
		
		if ( $match !== NULL )
		{
			return $this->pos() - $pos;
		}
		
		return NULL;
	}
	
	public function unscan()
	{	
		$this->pos($this->previousPointer);
		$this->match = NULL;
		
		return $this->pos();
	}
}