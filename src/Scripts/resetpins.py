#!/usr/bin/python

import sys, getopt, time, os
import RPi.GPIO as GPIO

GPIO.setmode(GPIO.BCM)
GPIO.setwarnings(False)

def main(args):
    GPIO.cleanup()

if __name__ == "__main__":
   main(sys.argv)