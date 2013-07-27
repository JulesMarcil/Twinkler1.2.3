<?php

namespace Tk\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * profilepicture
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Tk\UserBundle\Entity\ProfilePictureRepository")
 */
class profilepicture
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    public $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    public $path;
    
    /**
     * @Assert\File(
     *      maxSize="6000000"
     * )
     */
    public $file;

    public function getAbsolutePath()
    {
        return null === $this->path ? null : $this->getUploadRootDir().'/'.$this->path;
    }

    public function getWebPath()
    {
        return null === $this->path ? null : $this->getUploadDir().'/'.$this->path;
    }

    protected function getUploadRootDir()
    {
        // le chemin absolu du répertoire où les documents uploadés doivent être sauvegardés
        return __DIR__.'/../../../../web/'.$this->getUploadDir();
    }

    protected function getUploadDir()
    {
        // on se débarrasse de « __DIR__ » afin de ne pas avoir de problème lorsqu'on affiche
        // le document/image dans la vue.
        return 'uploads/profile-pictures';
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * Set path
     *
     * @param string $path
     * @return profilepicture
     */
    public function setPath($path)
    {
        $this->path = $path;
    
        return $this;
    }

    /**
     * Get path
     *
     * @return string 
     */
    public function getPath()
    {
        return $this->path;
    }
    
    public function upload($user)
    {
        // la propriété « file » peut être vide si le champ n'est pas requis
        if (null === $this->file) {
            return;
        }

        $extension=strrchr($this->file->getClientOriginalName(),'.');
        $tempname = 'temp'.$user->getId().$extension;
        $newname = $user->getId().'.jpg';

        $this->file->move($this->getUploadRootDir(), $tempname);

        $temppath = 'uploads/profile-pictures/'.$tempname;
        $newpath = 'uploads/profile-pictures/'.$newname;
        
        $this->compress_image($temppath,$newpath);
        $this->path = $newpath;
        
        unlink($temppath);
        $this->file = null;
    }
    
    function compress_image($source_url, $destination_url) 
        {
            $info = getimagesize($source_url);
            list ($width, $height) = getimagesize($source_url); 

            if ($info['mime'] == 'image/jpeg') $image = imagecreatefromjpeg($source_url);
            elseif ($info['mime'] == 'image/gif') $image = imagecreatefromgif($source_url);
            elseif ($info['mime'] == 'image/png') $image = imagecreatefrompng($source_url);
            
            $min = min($width, $height);
            
            if ($min == $width) {
                $a = 0;
                $b = ($height-$width)/2;
            }
            
            else {
                $a = ($width-$height)/2;
                $b = 0;
            }
            
            $new_image = imagecreatetruecolor( 160, 160 );
            imagecopyresampled($new_image, $image, 0,0,$a,$b, 160, 160, $width-$a, $height-$b);
            imagejpeg($new_image, $destination_url, 50);
        }
}
