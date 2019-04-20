#!/usr/bin/python

import sys, getopt, time, os
import RPi.GPIO as GPIO

GPIO.setmode(GPIO.BCM)
GPIO.setwarnings(True)

seqForward = [ [1,0,0,0],
        [1,1,0,0],
        [0,1,0,0],
        [0,1,1,0],
        [0,0,1,0],
        [0,0,1,1],
        [0,0,0,1],
        [1,0,0,1]]

seqBackward = [ [1,0,0,1],
        [0,0,0,1],
        [0,0,1,1],
        [0,0,1,0],
        [0,1,1,0],
        [0,1,0,0],
        [1,1,0,0],
        [1,0,0,0]]

def main(args):
    pin1 = -1
    pin2 = -1
    pin3 = -1
    pin4 = -1
    state = -1

    if len(args) == 1:
          print "empty parameter list, using: motor.py <pin1> <pin2> <pin3> <pin4> <state 1 = rolled up, 2 = rolled down>"
    else:
        if int(args[1]):
            pin1 = int(args[1])
            ControlPin[0] = pin1
        if int(args[2]):
            pin2 = int(args[2])
            ControlPin[1] = pin2
        if int(args[3]):
            pin3 = int(args[3])
            ControlPin[2] = pin3
        if int(args[4]):
            pin4 = int(args[4])
            ControlPin[3] = pin4
        if pin1 >= 0 and pin2 >= 0 and pin3 >= 0 and pin4 >= 0 and (state == 1 or state == 2):
            for pin in ControlPin:
                GPIO.setup(pin, GPIO.OUT)
                GPIO.output(pin, 0)

            if state == 2:
                for i in range(512):
                    for halfstep in range(8):
                        for pin in range(4):
                            GPIO.output(ControlPin[pin], seqBackward[halfstep][pin])
                            time.sleep(0.001)
                GPIO.cleanup()

            if state == 1:
                for i in range(512):
                    for halfstep in range(8):
                        for pin in range(4):
                            GPIO.output(ControlPin[pin], seqForward[halfstep][pin])
                            time.sleep(0.001)
                GPIO.cleanup()

            print 1
        else:
            print 0

if __name__ == "__main__":
   main(sys.argv)