//concordance_driver.cpp
//Mack:Alexander:A0398948:u17
//Submissions 08
//Building Concordances

// Everything should be to specification!

#include <cstdlib>
#include <string>
#include <fstream>
#include <iostream>
#include <iomanip>
#include <set>
using namespace std;
#include "Concordance.h"
#include "utilities.h"
using Scobey::Pause;
using Scobey::DisplayOpeningScreen;
using Scobey::TextItems;

int main(int argc, char* argv[])
{
    // if command-line args, print information screens
    if(argc == 1)
    {
        DisplayOpeningScreen("\t\tMack:Alexander:A00398948:u17",
                             "\n\t\tSubmission 07"
                             "\n\t\tBuilding Concordances");
        TextItems("./concordance.txt").TextItems::displayItem(
            "ProgramDescription");
        return 0;
    }
    else if(argc == 2 || argc == 3) // try and read the first file
    {
        // get file name from input
        string inFileName = argv[1];
        ifstream inFile(inFileName);
        // if file cannot be opened, error and close
        if(!inFile.is_open())
        {
            cout << "Could not open input file " << inFileName
                << ".\nProgram terminating." << endl;
            Pause();
            return 0;
        }
        // initialize concordance object
        Concordance conc(inFile);
        inFile.close();
        // if there are 2 command-line arguments, read the second one
        // and set it as output.
        if(argc == 3)
        {
            string outStreamName = argv[2];
            ofstream outStream(outStreamName);
            // if file cannot be opened, error and close.
            if(!outStream.is_open())
            {
                cout << "Could not open output file " << outStreamName
                    << ".\nProgram terminating." << endl;
                Pause();
                return 0;
            }
            // write concordance object to file.
            conc.write(outStream);
            outStream.close();
            // print confirmation
            cout << "Concordance has been built and output to the file " 
                << outStreamName << "." << endl;
        }
        else
        {
            // if only one command-line argument is given, set output to 
            // standard output
            conc.write(cout);
            // print confirmation
            cout << "Concordance has been built and output to the screen." 
                << endl;
        }
        Pause();
        return 0;
    }
    else // if too many arguments are given, error and close
    {
        cout << "Bad number of input parameters. Must be either 2 or 3.\n" <<
            "Names of input and (optionally) output files are missing.\n" <<
            "Program now terminating." << endl;
        Pause();
        return 0;
    }
}