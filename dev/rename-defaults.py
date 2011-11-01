import os, os.path
import fnmatch
import shutil

def renameDefaults ( dir ):
    for file in os.listdir (dir):
        if file[0] == ".":
            continue
    
        relpath = os.path.join (dir, file)
        if os.path.isdir (relpath):
            renameDefaults (relpath)
        elif fnmatch.fnmatch (file, "*.default"):
            shutil.copy (relpath, relpath[0:-8])
            print "Copied %s as %s" % (relpath, relpath[0:-8])
            
renameDefaults ("..")