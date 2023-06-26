//EncoderDecoder.cpp
//Mack:Alexander:A00398948:u17
//Submission 03
//Encoding and Decoding Text Files

/*
 * Everything should be to specification
 */

#include <iostream>
#include <fstream>
#include <string>
#ifndef ENCODER_DECODER_H
#define ENCODER_DECODER_H
using namespace std;

/**
 * The main constructor for the EncoderDecoder class, attempts to open
 * the files, terminates the program if it cannot.
 * @param inFileName the name/location of the input file
 * @param outFileName the name/location of the output file
 */
EncoderDecoder::EncoderDecoder(
    string inFileName,
    string outFileName
)
{
        // open input file
        this->inFile = ifstream(inFileName);
        // if the file cannot open print error message and terminate
        if (!this->inFile.is_open())
        {
            cout << "Problem opening file " << inFileName
                << ".\nProgram terminating." << endl;
            Pause();
            this->closeOpenedFiles();
            exit(0);
        }
        // open output file
        this->outFile = ofstream(outFileName);
        // if the file cannot open print error message and terminate
        if (!this->outFile.is_open())
        {
            cout << "Problem opening file " << outFileName
                << ".\nProgram terminating." << endl;
            Pause();
            this->closeOpenedFiles();
            exit(0);
        }
}

/*
 * The encoding function for the EncodeDecode class, takes input from
 * the input file line by line, char by char, and encodes to the output file.
 */
void EncoderDecoder::encodeInputFileToOutputFile(){
    // initialize variables
    string ascii; // the ascii code for the current character in the line
    string s; // the current line of the input file
    int fudger = 123; // the offset value
    int current; // the current char
    char temp;
    int count = 0; // the counter to keep the output clean
    // iterate through each line
    while (getline(this->inFile, s))
    {
        for (int i = 0; i < s.length(); i++)
        {
            // check line length, add new line at 60
            if (count == 60)
            {
                count = 0;
                this->outFile << "\n";
            }
            // add fudge factor
            current = (int)s[i] + fudger;
            ascii = to_string(current);
            // reverse string
            temp = ascii[0];
            ascii[0] = ascii[2];
            ascii[2] = temp;
            count += 3;
            this->outFile << ascii;
        }
        // check line length, add new line at 60 characters
        if (count == 60)
        {
            count = 0;
            this->outFile << "\n";
        }
        // add newline delimiter
        this->outFile << 433;
        count += 3;
    }
}
void EncoderDecoder::decodeInputFileToOutputFile(){
    // initialize variables
    string ascii; // the ascii value of the current character
    string s; // the current line of the input file
    int defudge; // the value to be "defudged" into correct ascii values
    // iterate through each line
    while (getline(this->inFile, s))
        // get chunks of the line, 3 digits at a time
        for (int i = 0; i<s.length(); i= i+3)
        {
            // reverse each triplet
            ascii = s[i+2];
            ascii = ascii + s[i+1];
            ascii = ascii + s[i];
            // convert to an integer
            defudge = stoi(ascii);
            // check for newline delimiter
            if(defudge == 334)
            {
                this->outFile << "\n";
            }
            else // defudge the value and convert ascii to char
            {
                defudge = defudge - 123;
                this->outFile << (char) defudge;
            }
        }
        
}
/*
 * The function to close the files of the EncoderDecoder class.
 */
void EncoderDecoder::closeOpenedFiles(){
    this->inFile.close();
    this->outFile.close();
}
#endif // ENCODER_DECODER_H