#!/usr/bin/python

import sys, getopt, time, os
import RPi.GPIO as GPIO

GPIO.setmode(GPIO.BCM)
GPIO.setwarnings(False)

def main(args):
   pin = -1
   mode = -1
   if len(args) == 1:
      print "empty parameter list, using: door.py <pin> <in/out>"
   else:
      if int(args[1]):
         pin = int(args[1])

      if int(args[2]):
         mode = int(args[2])

      if pin >= 0 and (mode == "in" or mode == "out"):
         if mode == "in":
            GPIO.setup(pin, GPIO.IN)

         if mode == "out":
            GPIO.setup(pin, GPIO.OUT)

         print "success"
      else:
         print 0

if __name__ == "__main__":
   main(sys.argv)