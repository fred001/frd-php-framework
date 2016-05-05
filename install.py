#!/usr/bin/env python
#-*- coding: utf-8 -*- 

import sys,os
import pygame
import time,random
import hashlib,shutil,time
import urllib2

from sys import exit
from random import randint 
from time import strftime
from time import sleep

"""

print "----Install---"
print "step 1: local setting"
print "step 2: remove unnecessary files of your project"
print ""
print "after install finished, you can delete install program self"
print ""

#path_local_setting=
root_path=os.path.dirname(os.path.abspath(__file__))
local_setting_path=root_path+"/local/setting.php"

f=open(local_setting_path,"r");
content=f.read();
f.close()

dbname=''
username=''
password=''

print ">Setting Database"
if content.find("DBNAME"):
  dbname=raw_input("Your DB Name :")


if content.find("USERNAME"):
  username=raw_input("Your DB Username :")


if content.find("PASSWORD"):
  password=raw_input("Your DB Password :")

content=content.replace("DBNAME",dbname)
content=content.replace("USERNAME",username)
content=content.replace("PASSWORD",password)

f=open(local_setting_path,"w");
f.write(content);
f.close()

print ">Database Setting Changed"
print ">>You can also change it by manual (%s)" %(local_setting_path)
print ""
print ">Delete Unnecessary Files"


files=[
  "README.md",
  "test",
  "doc",
  ".git",
]

for filename in files:
  if os.path.isfile(filename):
    print ">>delete %s"  %(filename)
    #os.unlink(filename)
  elif os.path.isdir(filename):
    print ">>delete %s/"  %(filename)
    #shutil.rmtree(filename)
"""


"""
Install : 
  copy file to dest
"""

TARGET_FOLDER=""

while not TARGET_FOLDER:
  TARGET_FOLDER=raw_input("please enter install folder path:\n")

  if not os.path.exists(TARGET_FOLDER):
    print "please enter an exists path"
    TARGET_FOLDER=""

TARGET_FOLDER=TARGET_FOLDER.rstrip("/")
#print TARGET_FOLDER

#copy files
COPY_FILES=[
  'default_setting.php',
  'functions.php',
  'lib/',
  'local/',
  'modules/',
  'public/',
]

for filename in COPY_FILES:
  dest=TARGET_FOLDER+"/"+filename
  if os.path.isfile(filename):
    shutil.copy(filename,dest)
  elif os.path.isdir(filename):
    shutil.copytree(filename,dest)

  print "copy %s ==>  %s" %(filename,dest)

#remove module's blog code
shutil.rmtree(TARGET_FOLDER+"/modules/default/controller/blog")
shutil.rmtree(TARGET_FOLDER+"/modules/default/templates/blog")
os.unlink(TARGET_FOLDER+"/modules/default/Object/Blog.php")
os.unlink(TARGET_FOLDER+"/modules/default/Table/Blog.php")


print "install finished !"
print ""
print "\tnow please setup your web server with the documentroot:"
print "\t"+TARGET_FOLDER+"/public"
print ""
print "\tthen config the db setting in \n\t%s" %(TARGET_FOLDER+"/local/setting.php")

