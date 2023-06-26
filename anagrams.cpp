//anagrams.cpp
//Mack:Alexander:A0398948:u17
//Submissions 03
//Finding anagrams


/* 
 * Everything from anagrams.cpp should be up to specification!
 * However while it was not explicitely required, I was unable
 * to fully automate the program via my_tests.sh since I
 * could not figure out how to properly feed input to the
 * program with bash.
 */

#include <cstdlib>
#include <string>
#include <fstream>
#include <iostream>
#include <vector>
#include <algorithm>
using namespace std;

#include "utilities.h"
using Scobey::Pause;
using Scobey::TextItems;
using Scobey::DisplayOpeningScreen;

int main(int argc, char* argv[])
{
    // if no dictionary, print information screens
    if (argc == 1)

    {
        DisplayOpeningScreen("\t\tMack:Alexander:A00398948:u17",
            "\n\t\tSubmission 04"
            "\n\t\tFinding anagrams.");
        TextItems("./anagrams.txt").TextItems::displayItem(
            "ProgramDescription");
        return 0;
    }
    // if proper number of inputs are given, begin program
    if (argc == 2)
    {
        // initialize variables
        string inFileName = argv[1];
        string s;
        string word;
        // open dictionary
        ifstream inFile(inFileName);
        // if file cannot be opened
        if (!inFile.is_open())
        {
            cout << "Could not open word file named " << inFileName
                << ".\nProgram terminating." << endl;
            Pause();
            return 0;
        }
        // initialize dictionary
        vector<string> dictionary{};
        cout << "Reading the word file ..." << endl;
        // iterate through file and push to vector
        while (getline(inFile, s))
        {
            dictionary.push_back(s);
        }
        // sort the dictionary for binary search
        sort(dictionary.begin(), dictionary.end());
        cout << "The word file " << inFileName <<
            " contains " << dictionary.size() << " words." << endl;
        Pause();

        // take user input for word
        cout << 
            "Now enter a word (or any string of letters) and I'll give you\n"
            "a list of all of its anagrams (if any) found in the dictionary: ";
        cin >> word;

        // check that the word was not the special character eof, 
        // loop until it is given
        while (!cin.eof())
        {
            // initialize bool to print if no matches are found
            bool found = false;
            // set the word to a character vector for permutation and sorting
            vector<char> pWord(word.begin(), word.end());
            sort(pWord.begin(), pWord.end());

            // initialize string of initial sorted word
            string anagram(pWord.begin(), pWord.end());

            // check if the sorted word is in the dictionary
            if (binary_search(dictionary.begin(), dictionary.end(), anagram))
            {
                cout << anagram << endl;
                found = true;
            }

            // iterate through the permutations 
            // and print if they are in the dictionary
            while (next_permutation(pWord.begin(), pWord.end()))
            {
                // take snapshots of the permutations as strings
                string anagram(pWord.begin(), pWord.end());
                if (binary_search(dictionary.begin(),
                        dictionary.end(), anagram))
                {
                    cout << anagram << endl;
                    found = true;
                }
            }

            // if no matches are found, print special output
            if (!found)
            {
                cout << "Sorry, no anagrams found for that input." << endl;
            }
            cout << endl;
            // take more user inputs or eof character
            cout << "Enter another one "
                "(or the end-of-file character ctrl+D to stop): ";
            cin >> word;
        } // loop back unless eof is given

        // terminate program
        cout << endl;
        cout << "OK ... no more entries." << endl;
        cout << "Program is now terminating." << endl;
        Pause();
        return 0;
    }
    else // print error and terminate
    {
        cout << "Bad number of input parameters!\n"
            "Must be exactly one.\n"
            "Program now terminating" << endl;
        Pause();
        return 0;
    }
}