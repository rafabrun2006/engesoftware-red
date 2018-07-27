<?php
/**
 * Created by PhpStorm.
 * User: rafael
 * Date: 26/07/18
 * Time: 18:07
 */

namespace Engesoftware;


class File
{
    protected $name;
    protected $extension;
    protected $file;

    public function __construct($name, $extension, $file)
    {
        $this->setName($name);
        $this->setExtension($extension);
        $this->setFile($file);
    }

    public function getName()
    {
        return $this->name();
    }

    public function getExtension()
    {
        return $this->extension;
    }

    public function getFile($file)
    {
        return $this->file;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function setExtension($extension)
    {
        $this->extension = $extension;
    }

    public function setFile($file)
    {
        $this->file = $file;
    }

}
