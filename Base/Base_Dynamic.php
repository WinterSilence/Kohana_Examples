<?php
/**
 * Abstract base class, supports dynamic work with properties.
 * 
 * @abstract
 */
abstract class Base_Dynamic
{
	/**
	 * Gets hidden (private or protected) property.
	 * Utilized for reading data from inaccessible properties.
	 * if it is not found special method, gets the property directly.
	 * 
	 * @param   string  $name   property name
	 * @return  mixed
	 */
	public function __get($name) //public function & __get($name)
	{
		if (method_exists($this, 'get_'.$name))
		{
			// Gets the property using a method - getter 'get_`property_name`'.
			return $this->{'get_'.$name}();
		}
		elseif (method_exists($this, $name))
		{
			// Gets the property using a multimethod '`property_name`'.
			return $this->$name();
		}
		elseif (property_exists($this, '_'.$name))
		{
			// Gets the property directly.
			return $this->{'_'.$name};
		}
		// Throw an exception if the property is not exists
		$error = sprintf('Property %s not exists', htmlspecialchars($name));
		throw new InvalidArgumentException($error);
	}

	/**
	 * Sets hidden (private or protected) property.
	 * Run when writing data to inaccessible properties.
	 * if it is not found special method, sets the property directly.
	 * 
	 * @param   string  $name   property name
	 * @param   mixed   $value  property value
	 * @return  void
	 */
	public function __set($name, $value)
	{
		if (method_exists($this, 'set_'.$name))
		{
			// Sets the property using a method - setter 'set_`property_name`'.
			$this->{'set_'.$name}($value);
		}
		elseif (method_exists($this, $name))
		{
			// Sets the property using a multimethod '`property_name`'.
			$this->$name($value);
		}
		elseif (property_exists($this, '_'.$name))
		{
			// Sets the property directly.
			$this->{'_'.$name} = $value;
		}
		else
		{
			// Throw an exception if the property is not exists
			$error = sprintf('Property %s not exists', htmlspecialchars($name));
			throw new InvalidArgumentException($error);
		}
	}

	/**
	 * Determine if a variable is set and is not NULL.
	 * Triggered by calling isset() or empty() on inaccessible properties.
	 * 
	 * @param   string  $name  property name
	 * @return  boolean
	 */
	public function __isset($name)
	{
		return property_exists($this, '_'.$name);
	}

	/**
	 * Working with properties via virtual methods.
	 * Triggered when invoking inaccessible methods in an object context.
	 * 
	 * @param   string  $name       method name
	 * @param   array   $arguments  method arguments
	 * @return  mixed
	 */
	public function __call($name, $arguments)
	{
		if (property_exists($this, '_'.$name))
		{
			$property = '_'.$name;
		}
		elseif (property_exists($this, $name))
		{
			$property = $name;
		}
		elseif (preg_match('~^(set|get)_([a-z])([\w]*)$~i', $name, $matches))
		{
			$property = $matches[2].$matches[3];
			if ( ! property_exists($this, $property))
			{
				$property = '_'.$property;
				if ( ! property_exists($this, $property))
				{
					unset($property);
				}
			}
		}

		if (isset($property))
		{
			$cnt = count($arguments);
			if ($cnt === 0)
			{
				if ( ! isset($matches) OR $matches[1] == 'get')
				{
					return $this->$property;
				}
			}
			elseif ($cnt === 1)
			{
				if ( ! isset($matches) OR $matches[1] == 'set')
				{
					$this->$property = $arguments[0];
					return $this;
				}
			}
		}
		// Throw an exception if the property is not exists
		$error = sprintf('Method %s not exists', htmlspecialchars($name));
		throw new InvalidArgumentException($error);
	}

}