<?php
/**
 * Copyright (C) 2013-2016
 * Piotr Olaszewski <piotroo89@gmail.com>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */
namespace WSDL\Parser;

use WSDL\Types\Type;

/**
 * MethodParser
 *
 * @author Piotr Olaszewski <piotroo89@gmail.com>
 */
class MethodParser
{
    /**
     * @var string
     */
    private $_name;
    /**
     * @var string
     */
    private $_doc;
    /**
     * @var array
     */
    private $_rawParameters;
    /**
     * @var string
     */
    private $_rawReturn;

    public function __construct($name, $doc)
    {
        $this->_name = $name;
        $this->_doc = $doc;
    }

    /**
     * @return string
     */
    public function description()
    {
        preg_match('#@desc (.+)#', $this->_doc, $groupMatches);
        $trimGroupMatches = array_map('trim', $groupMatches);
        return !empty($trimGroupMatches[1]) ? $trimGroupMatches[1] : '';
    }

    /**
     * @return Type[]
     */
    public function parameters()
    {
        preg_match_all('#@param (.+)#', $this->_doc, $groupMatches);
        $this->_rawParameters = $groupMatches[1];
        return ParameterParser::create($groupMatches[1], $this->getName());
    }

    /**
     * @return Type
     */
    public function returning()
    {
        preg_match('#@return (.+)#', $this->_doc, $groupMatches);
        $trimGroupMatches = array_map('trim', $groupMatches);
        if (isset($trimGroupMatches[1])) {
            $this->_rawReturn = $trimGroupMatches[1];
        }
        $parameterParser = new ParameterParser($this->_rawReturn, $this->getName());
        return $parameterParser->parse();
    }

    /**
     * @return string
     */
    public function getDoc()
    {
        return $this->_doc;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * @return array
     */
    public function getRawParameters()
    {
        $this->parameters();
        return $this->_rawParameters;
    }

    /**
     * @return string
     */
    public function getRawReturn()
    {
        $this->returning();
        return $this->_rawReturn;
    }
}
