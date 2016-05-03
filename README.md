# frd-php-framework

description:
  a small ,quick php framework

feature:
    易于阅读，易于使用 ，对程序员友好
    稳定 ， 有完善的测试代码
    可扩展 ，可以扩展本身的功能， 替换组件，
    易于集成，可以方便加入第三方库
    模块管理 ， 可以增加模块，增强功能
    基於 Zend Frameworkd v1 ,可以重用 zend 功能
    模仿Yii, 具备Yii 的框架功能

INSTALL:
    OS Requirement:    unix/linux  (but only tested in centos7)
    Document: <a target="_blank" href="http://iamlosing.me/frd_framework">document</a>
    Install: 
        git clone https://github.com/fred001/frd-php-framework.git
        cd frd-php-framework
        python install.py
            (enter the website's document root path (TARGET_PATH))

        after install finished
          1, setup your webservice with the document root (TARGET_PATH/public)
          2, setup website's setting (TARGET_PATH/local/setting.php)

          3, visit the website ,no error and see the "hello world",that means install successful.


Tutorial: use frd framework create  blog manage pages

  first make sure you have installed the framework as the INSTALL part described


  1.setup setting (local/setting.php)
  2. create database table
    run sql:
        
      create table blog (
          id int(10) not null auto_increment primary key,
          title char(100) not null default '',
          content text);

  now try create blog function
  need 4 pages:  list, add ,edit,delete
  before work on these pages, create some basic function first
  normally a website's page wil need a layout + current page's content
  so first create the basic layout

  1.create layout template:
    modules/default/templates/layout/bootstrap.phtml (here use bootstrap's style)

  2.create a public method for get the layout 
    modules/default/main.php:getLayout($name) 

    this method return a template object with the layout template file

  now is time to create the blog pages

  blog list page:
    1. define the url   /blog/list
        so the controller file is   modules/default/controller/blog/list.php

    2.create the file  modules/default/controller/blog/list.php
      is this controller file choose render a template file "blog/list"
      so the template file is  modules/default/templates/blog/list.phtml

      of course it is free to choose render other template file,
      but normally render the same name of controller

    3.create the template file   modules/default/templates/blog/list.phtml
    4.the detail you can look at these files,now the list page finished,
      and visit it by url : DOMAIN/blog/list


  now create blog add page, in order to add record
    1. define url:  blog/add
    2. create controller :  modules/default/controller/blog/add.php
    3. create template : modules/default/templates/blog/add.phtml
    4. visit it by url: DOMAIN/blog/add
        now try add blog record

        in controller blog/add.php ,you may see the   $_module->getTable("blog")
        the $_module is the module object (modules/default/main.php )
        it's class  Index
        and getTable is load the class  modules/default/Table/Blog.php
        the class Index_Table_Blog

        if the code is $_module->getTable2("blog2")
        the class should be   Index_Table2_Blog2 
        and file should be modules/default/Table2/Blog2.php

  now for edit page
    1. define url : blog/edit
    2. so controller is : modules/default/controller/blog/edit.php
    3. and template : modules/default/templates/blog/edit.phtml
    4. now visit the url : DOMAIN/blog/list
        and choose an edit url to click (make sure you have added some records)

  at last delete page 
    1. define url: blog/delete
    2. create controller:  modules/default/controller/blog/delete.php
    3. here do not need template, so do not need create it
    4. visit list page, and click one delete page
        it should delete the record and redirect back to list page

    
    
API Documents:
  preparing

