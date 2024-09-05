<?php
namespace App\Services;

use DOMDocument;

class ArrayToXml {

    protected $parent;
    protected $process;

    public function  __construct() {
        $this->process = new DOMDocument();
    }

    public function rootTag($parent)
    {
        $this->parent = $this->process->createElement($parent);
    }

    public function appendToRootTag($tagName, $content = null)
    {
        $this->parent->appendChild($this->process->createElement($tagName, $content));
    }

    public function appendToElement($sibling, $tagName, $content = null, $key = 0)
    {
       $sibling =  $this->parent->getElementsByTagName($sibling)->item($key);
       $sibling->appendChild($this->process->createElement($tagName, $content));
    }

    public function getContent() {
        return $this->process->saveXML($this->parent, LIBXML_NOEMPTYTAG);
    }
}
