#!/bin/python
import os, sys
"""
create thumbnail for a image

Usage:
  python create_thumbnail.py  PATH_IMAGE PATH_THUMBNAIL THUMBNAIL_W THUMBNAIL_H
"""

def usage():
  print("Frd Tool-create_thumbnail")
  print("Usage:")
  print("python create_thumbnail.py  PATH_IMAGE PATH_THUMBNAIL THUMBNAIL_W THUMBNAIL_H")

if len(sys.argv) == 2 and sys.argv[1] == 'usage':
  usage()
  sys.exit(0)

if len(sys.argv)  < 5:
  print("Frd Tool-create_thumbnail:invalid params")
  sys.exit(1)

path_image=sys.argv[1]
path_thumbnail=sys.argv[2]
w=int(sys.argv[3])  #should be integer,if is string, will not effect the thumbnail size
h=int(sys.argv[4])  #same as w

size=(w,h)

#run
from PIL import Image

im = Image.open(path_image)
im.thumbnail(size, Image.ANTIALIAS)
im.save(path_thumbnail)
