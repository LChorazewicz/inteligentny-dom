#!/usr/bin/python

import sys, getopt, time, os
import RPi.GPIO as GPIO

GPIO.setmode(GPIO.BCM)
GPIO.setwarnings(False)

def main(args):
   pin = -1
   state = -1
   if len(args) == 1:
      print "empty parameter list, using: door.py <pin> <1 = high, 2 = low>"
   else:
      if int(args[1]):
         pin = int(args[1])

      if int(args[2]):
         state = int(args[2])

      if pin >= 0 and (state == 1 or state == 2):
         if state == 1:
            GPIO.output(pin, GPIO.HIGH)

         if state == 2:
            GPIO.output(pin, GPIO.LOW)

         print "success"
      else:
         print 0

if __name__ == "__main__":
   main(sys.argv)