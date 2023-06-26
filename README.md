# sturdy-fishstick
This repository will be a collection of assorted projects that I have worked on for my own personal use. If you like any of this please feel free to use it just make sure to credit me if you make it big!

# IRC_Client
This folder contains the workings of a C# based IRC client, to be used in tandem with the IRC_Server. It has a GUI that accepts messages from the user, and receives messages from the server. It uses RSA and AES encryption to protect transmissions. The program is designed for Windows machines, using windows forms and some VB objects. Currently the IP address is incorrectly set for network safety, so that will need to be changed in order for the program to function.

# IRC_Server
This folder contains the workings of a C# based IRC server, to be used for the IRC_Client. This server accepts messages from users, and echoes them off to all other connected users. It uses RSA and AES encryption to protect the messages in transmission. It is designed to be run from the command line, and has been properly working on an Ubuntu 22.04 laptop. Currently the IP address is incorrectly set for network safety, so nothing will connect to the server unless that is changed.

# dining_philosophers.cpp
This program is a psuedo-solution to the "dining philospher" problem, in which N (in this case 5) philosophers must use two "forks" each to eat their food, then think for some time before becoming hungry again. The purpose of this program is to manage the forks amongst the philosophers such that the philosophers may no longer eat. This program creates this effect by creating a thread for each philosopher, with a set of shared values for their forks, with semaphores put in place to prevent a deadlock.

# matrix_add.c
This program generates two randomized 4 by 4 matrixes, and using multithreading, performs matrix addition, with each thread performing the addition of one row.

# matrix_sub.c
This program generates two randomized 4 by 4 matrixes, and using multithreading, performs matrix substraction, with each thread performing the substraction of one row.

# trapezoidal_approximation.c
This program approximates an integral through the trapezoidal rule. The program creates child processes as workers to make the calculations, and pass those back to the parent to be summed together. The number of children that are created is given by the user as a command line argument (between 1 and 8).

# bus_simulation.c
This program simulates an odd intersection of buses that only require being the first bus in the queue, as well as not allowing the bus to their right to be crossing in order to cross. This program takes a file with the directions from which all the buses are approaching (in order) as well as a command line argument for the probability that one of these buses is added to the queue. The program will then either add a bus or check for a deadlock (if all buses are waiting for the bus on the right). Once all the buses have been added to the queue, the program will check for a deadlock once each second. The purpose of this program is to practice implementing deadlock detection, as well as practice semaphore management.

## The following programs include "utilities.h" among other header files or drivers that were provided courtesy of Professor Porter Scobey. They are not included in this library. Their purpose will be explicitely described for each program listed.

# eval_pre_pst.cpp
This program evaluates any valid prefix or postfix expression which contains positive integer operands and the operators +, -, * and/or /. The expression  to be evaluated must be entered on the command line within double quotes. Note that a single positive integer simply evaluates as itself. 
This program uses utilities.h to provide a Pause() function that would wait for the user to proceed, as well as a DisplayOpeningScreen method that would format the desired text to produce an information screen.

# node_sequence.cpp
This program takes 5 command line arguments, the first argument designates the lowest number of a node sequence of numbers, the second argument designates the highest number of the sequence. The program uses these two arguments to create a sequence of numbers from the lowest to highest, incrementing one at a time. The third argument denotes the order, either appedning each next node to the b (beginning) or e (end) of the sequence, the fourth argument denotes a new value to be appended to the node sequence, and finally the last argument designates which value the new number should be input after. So in all, this program will create a range of numbers, either ascending or descending, and then inject a new number into this sequence at the desired location, with displays after each major change. This program achieves this through manipulation of nodes and node pointers.
This program uses Pause and DisplayOpeningScreen, as well as TextItems which uses a preformatted text document for the opening screen.

# NodeSequence.cpp
This program is for a driver provided by Professor Porter Scobey. The driver ideally provides the exact same output as node_sequence.cpp, with this program filling in the gaps in the driver file to do so. 
This program will not run without the driver.

# Expression.cpp
This program is an extension of a driver program "compute" which is provided by Professor Porter Scobey. This program implements a header file which was also provided to create an expression tree from any general arithmetic function. This program takes a recursive approach to generating the tree.

# anagrams.cpp
This program uses a dictionary, provided by Professor Porter Scobey, to take any input given by the user, and determine what possible anagrams exist with the given set of characters.
This program uses the dictionary provided, which can be substituted, and the utilities.h file for the Puase, TextItems, and DisplayOpeningScreen functions.

# EncoderDecoder.cpp
This file is a class file that implements a set of methods. This class can encode a given file, with a simple algorithm, and print the output to a text file. Or decode a file which has been previously encoded using the same algorithm.
This file requires encode_decode.cpp as the driver.

# encode_decode.cpp
This program is the driver for the EncoderDecoder class file. Which verifies the command line arguments and outputs the results as desired by the user.
This program requires the header file EncoderDecoder.h which was provided by Professor Porter Scobey.

# power_sets_of_int.cpp
This program takes a command line argument for the number of values in a given set, The program will then create the power set for that number of integers in that set. ie. a command line argument of 4 will provide a set of {1,2,3,4} and all subsets of that set.
This program uses utilities.h for the Pause and DisplayOpeningScreen functions.

# Concordance.cpp
This program is a class file that implements Concordance.h, which was provided by Professor Porter Scobey. These functions are used in the driver to generate a concordance for any given file, and to print the concordance to the standard output or to a designated file.
This program is to be compiled with concordance_driver.cpp as the main driver.

# concordance_driver
This program is the driver file for the Concordance.h header file, provided by Professor Porter Scobey, to generate a concordance for any given file.
This program uses utilities.h for the Pause, DisplayOpeningScreen, and TextItems functions.
