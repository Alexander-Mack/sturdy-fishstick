//power_sets_of_int.cpp
//Mack:Alexander:A0398948:u17
//Submissions 07
//Building and Displaying Power Sets of Integers


// Everything should be to specification!

#include <cstdlib>
#include <string>
#include <fstream>
#include <iostream>
#include <set>
#include <math.h>
using namespace std;
#include "utilities.h"
using Scobey::Pause;
using Scobey::DisplayOpeningScreen;

int count = 0; // global integer for formatting

/**
 * This function creates and prints every combination from a given
 * head value.
 * This function appends the head value to the given set, prints the current
 * set, and then if the head value is not equal to the end value, increments
 * the head value and recursively calls the function. 
 * @param head the next value to add to the set
 * @param end the limiting value for the head value
 * @param combo the set to be appended to and printed.
 */
void Combination(int head, int end, set<int> combo);

int main(int argc, char* argv[])
{
    // if no value, print information screens
    if(argc == 1)
    {
        DisplayOpeningScreen("\t\tMack:Alexander:A00398948:u17",
                             "\n\t\tSubmission 07"
                             "\n\t\tBuilding and Displaying Power " 
                             "Sets of Integers");
        cout << 
R"(The power set of any set is the set of all subsets of the given set,
and it contains 2^n (2 to the power of n) elements.

This program reads from the command line (without any error checking) a
single non-negative integer value n representing the number of values in
the set whose power set is to be found. If n is 0, that set is empty and
the power set is also empty. Otherwise the input set is

{1, 2, 3, ..., n}

and it is the power set of that input set that is built and displayed.

The program first displays the size of the generated power set, and then
displays each set in that power set, using this format for each set

{e_1, e_2, ..., e_m}

with the sets themselves being displayed four per line, and with two
blank spaces between sets. Run the program several times with input
values 0, 1, 2, 3, ... to see what the output looks like.


                                                                Screen 1 of 1
)";
        Pause();
        return 0;
    }
    else // create sets and do good things
    {
        // take first user argument
        char* input = (char*)argv[1];
        int setNum = atoi(input);
        // print initial info
        cout << "\nNumber of subsets = " << pow(2,setNum) 
            << ", and here they are:";
        // initialize an empty int set
        set<int> combo;
        // print the empty set
        cout << endl << "{}  ";
        // initialize each head value for the recursive function
        for(int i = 1; i<=setNum; i++)
        {
            Combination(i, setNum, combo);
        }
        cout << endl;
        Pause();
        return 0;
    }
}

void Combination(int head, int end, set<int> combo)
{
    count++;
    // add head value to set
    combo.insert(head); 
    // initialize iterator
    set<int>::iterator iter = combo.begin();
    if(count%4==0) cout << endl;
    cout << "{";
    // iterate through current combination
    while(iter != combo.end())
    {
        cout << *iter++;
        if(iter != combo.end()) cout << ", ";
    }
    cout << "}  ";
    // if the head value has not reached the end value, increment and recurse
    for(int i = head+1;i<=end;i++)
    {
        Combination(i, end, combo);
    }
}