#!/usr/bin/python

import sys, getopt, time, os
import RPi.GPIO as GPIO

GPIO.setmode(GPIO.BCM)
GPIO.setwarnings(True)

ControlPin = [12, 16, 20, 21]

for pin in ControlPin:
        GPIO.setup(pin, GPIO.OUT)
        GPIO.output(pin, 0)

seq = [[1,0,0,1],
        [0,0,0,1],
        [0,0,1,1],
        [0,0,1,0],
        [0,1,1,0],
        [0,1,0,0],
        [1,1,0,0],
        [1,0,0,0]]

for i in range(512):
        for halfstep in range(8):
                for pin in range(4):
                        GPIO.output(ControlPin[pin], seq[halfstep][pin])
                time.sleep(0.001)
GPIO.cleanup()

if __name__ == "__main__":
   main(sys.argv)