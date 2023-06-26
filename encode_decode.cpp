//encode_decode.cpp
//Mack:Alexander:A00398948:u17
//Submission 03
//Encoding and Decoding Textfiles

/*
 * Everything should be to specification!
 */

#include <iostream>
#include <fstream>
#include <string>
#include "EncoderDecoder.h"
#include "EncoderDecoder.cpp"
using namespace std;

int main(int argc, char* argv[])
{
    // if no inputs, print info
    if (argc == 1 )
    {
        DisplayOpeningScreen("\t\tMack:Alexander:A00398948:u17",
                             "\n\t\tSubmission 03"
                             "\n\t\tEncoding and Decoding Text Files.");
        TextItems("./encode_decode.txt").TextItems::displayItem(
            "ProgramDescription");
        return 0;
    }
    // if 3 inputs are given, proceed
    if(argc == 4)
    {
        string mode = argv[1]; // check mode type
        // if mode type does not match, print error and terminate
        if (mode.compare("e") != 0 && mode.compare("d") != 0)
        {
            cout << "Error: Bad first parameter (must be e or d)."
            << "\nProgram now terminating." << endl;
            Pause();
            return 0;
        }
        // assign file names
        string inFileName = argv[2], outFileName = argv[3];
        // create EncoderDecoder object
        EncoderDecoder encDec(inFileName, outFileName);
        // if mode is "encoding", encode input to output
        if(mode.compare("e") == 0)
        {
            encDec.encodeInputFileToOutputFile();
            cout << "The input file " << inFileName 
            << " has been encoded and output to the file " 
            << outFileName << ".\n";
        }
        // if mode is "decode", decode input to output
        else if(mode.compare("d") == 0)
        {
            encDec.decodeInputFileToOutputFile();
            cout << "The input file " << inFileName 
            << " has been decoded and output to the file " 
            << outFileName << ".\n";
        }
        // if redundant catch in case initial check is bypassed
        else
        {
            cout << "Please select a valid option for argument";
        }
        // close opened files
        encDec.closeOpenedFiles();
    }
    // if an incorrect number of inputs are given, print error and terminate
    else
    {
        cout << "Bad number of command-line inputs!\n"
            "Must be exactly 2: the input and the output file names." << endl;
        Pause();
        return 0;
    }
}