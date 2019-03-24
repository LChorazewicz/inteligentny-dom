#!/usr/bin/python

import sys, getopt

def main(argv):
   pin = ''
   try:
      opts, args = getopt.getopt(argv,"p:")
   except getopt.GetoptError:
      print 'door.py -p <which pin>'
      sys.exit(2)
   for opt, arg in opts:
      if opt == '-p':
         pin = arg
   print 1

if __name__ == "__main__":
   main(sys.argv[1:])