#include <stdio.h>
#include <unistd.h>
#include <string.h>
#include <stdlib.h>
// define read and write ends of the file descriptor pipes
// for ease of access, as well as limit the message buffer to 8 bits
#define READ_END 0
#define WRITE_END 1
#define BUFFER_SIZE 8

// msgbuffer is the reusable char array made specifically for the fd pipes
char* msgbuffer[BUFFER_SIZE];

int main(int argc, char* argv[])
{
    pid_t pid = 1;
    // check for command line arguments, 
    // if there are none, then argc will equal 1
    if(argc == 1)
    {
        // print an information block and then close the program
        printf("This program approximates an integral through the "
            "trapezoidal rule. The program creates child processes\n"
            "as workers to make the calculations, and pass those back "
            "to the parent to be summed together.\nThe number of "
            "children that are created is given by the user as a command "
            "line argument. (between 1 and 8)\n");
        return 0;
    }
    // check if there is one single command line argument, if the argument 
    // is not an integer, or between 1 and 8, then print an error message 
    // and exit the program.
    else if(argc == 2)
    {
        // init sum to 1 to omit the first trapezoid
        double sum = 1;
        // get number of children desired by user
        int children = atoi(argv[1]);
        // if not in bounds, error and close
        if(children > 8 || children < 1)
        {
            printf("Please give a number of processes between 1 and 8.\n"
                "Program Terminating.\n");
            return 0;
        }
        // create pipe, only one is needed since the parent will not
        // need to send information to the child, only receive it.
        int fd[2];
        if (pipe(fd) < 0)
            return(0);
        // init integers to track children ownership
        int share;
        int parts = 16;
        int init_parts = parts;
        // for each child process, determine how many parts they are
        // in charge of and assign that many parts to them.
        for(int i = 0; i<children; children--)
        {
            share = parts/children; // truncated integer
            parts = parts - share; // reduce remaining parts
            // if this is the parent process, fork
            if(pid != 0)
            {
                pid = fork();
                // if current child, begin calculations
                if(pid == 0){
                    close(fd[READ_END]);
                    double calc;
                    // iterate through the 'owned' parts and calculate
                    for(share; share > 0; share--)
                    {
                        double x = share + parts;
                        // the very first trapezoid = 1, but should not
                        // ever be reached so this is mostly a sanity check
                        if(x != 0)
                        {
                            calc = 2*(((x/(init_parts/2))*(x/(init_parts/2))) + 1);
                        }
                        else
                        {
                            calc = 1;
                        }
                        // the very last trapezoid does not get multiplied
                        // by 2, so divide that back out if it is.
                        if(x == init_parts)
                        {
                            calc /= 2;
                        }
                        // convert calc to a char array
                        sprintf(msgbuffer, "%f", calc);
                        // write to pipe
                        write(fd[WRITE_END], msgbuffer, BUFFER_SIZE);
                    } // end of interior for loop
                    // close child
                    return 0;
                }
            }
        } // end of for loop
        // parent process
        if(pid > 0){
            close(fd[WRITE_END]);
            // wait for child processes to complete
            wait(NULL);
            // there should be 32 items within the pipe
            // iterate through and collect the values.
            for(int i = 0; i<init_parts; i++){
                read(fd[READ_END], msgbuffer, BUFFER_SIZE);
                // add each value to the sum
                sum += atof(msgbuffer);
            }
            // sum should be equal to about 149.312 before division
            // divide the sum by 32 to get the actual approximation
            sum = sum/init_parts;
            // print out results
            // if statement for formatting
            if(atoi(argv[1]) == 1)
            {
                printf("The approximation with %d child is %f\n",
                    atoi(argv[1]), sum);
            }
            else
            {
                printf("The approximation with %d children is %f\n", 
                    atoi(argv[1]), sum);
            }
        }
        return 0;
    }
    // if given too many command line arguments, print error message and terminate.
    else
    {
        printf("Please only give one other command line argument.\n"
            "Terminating Program.\n");
        return 0;
    }
    return 0;
}