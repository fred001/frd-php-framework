<?php
   /**
   *  handle image
   *
   *  TODO:
   *  wideimage  seems powerful
   *  so this class can add more powerful method
   *  the method for different image format should be easy to use
   *  *  add more methods
   *  *  be an interface
   *  *  add unit test  
   *
   */

   class Frd_Image
   {
      protected $image=null; //image object

      function __construct()
      {
         $this->loadLibs();
      }

      /**
      * load image lib, this lib is the real image handler
      */
      protected function loadLibs()
      {
         include_once(dirname(__FILE__).'/../wideimage/WideImage.php');
      }

      /**
      * load an image
      */
      function load($filename) 
      {
         $this->image=WideImage::load($filename);

         return $this->image;
      }

      /**
      * get image, only for it self  to use
      */
      protected function getImage()
      {
         if($this->image == false)
         {
            throw new Exception("image is not loaded, please use  load(PATH) to load it ");
         }

         return $this->image;
      }

      protected function setImage($image)
      {
         $this->image=$image;
      }

      /**
      * save the image  to harddisk
      */
      function save($filename)
      {
         //create the folder first, if not exists
         Frd_File::createDirByFilePath($filename);

         $this->getImage()->saveToFile($filename);
      }

      //asString  :  image to string 

      /**
      * output to browser
      */
      function output($image_type='jpg') 
      {
         //$this->image->output($image_type, 45);
         $this->getImage()->output($image_type);
      }
      function getWidth() 
      {
         return $this->getImage()->getWidth();
      }

      function getHeight() 
      {
         return $this->getImage()->getHeight();
      }

      //return (width,height) or false (if not image)
      function getSize() 
      {
         return array($this->getWidth(),$this->getHeight());
         //return $this->getWidth().",".$this->getHeight();
      }

      /**
      * resize a image
      * i guess it will work like this:
      *  it will always keep the image's proportion (w/h)
      *  if width >  resize_width
      resize by width
      else:
      resize by height
      */
      function resize($width,$height) 
      {
        //resize keep proportion, height may too large or too small
         //$image=$this->getImage()->resize($width, $height,'inside','down');
         $image=$this->getImage()->resize($width, null,'fill');

         if($height)
         {
           //crop  if height too large
           $real_height=$this->getHeight();
           if($real_height > $height)
           {
             //left,top,width,height 
             $image=$image->crop(0,0,$width,$height);
           }
         }
         //
         $this->setImage($image);

         return $image;
      }

      /*
      function resizeToHeight($height) 
      {
         $this->resize(null, $height);
      }

      function resizeToWidth($width) 
      {
         $this->resize($width,null);
      }
       */

      function scale($scale) 
      {

      }

      function getImageType()
      {

      }

      function rotate($angle, $bgColor = null, $ignoreTransparent = true)
      {
        return $this->image->rotate($angle, $bgColor , $ignoreTransparent );
      }
   }
