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


sys.path.append('/home/frd/lib/python')
import db
from functions import *


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



