// Concordance.cpp
//Mack:Alexander:A0398948:u17
//Submissions 08
//Building Concordances

#include "Concordance.h"
// private variables/definitions in Concordance.h
// typedef set<int> SetType;
// typedef map<string, SetType> ConcordanceType;
// ConcordanceType data;

// Everything should be to specification!

/**
 * This method is the constructor for the Concordance class object.
 * It takes a file as input, and creates a map of sets, with each
 * word in the file as a key. Each set keeps track of which lines
 * that word appears on.
 * @param inFile the input file given by the user
 */
Concordance::Concordance(ifstream& inFile)
{
    string s, word = "";
    int count = 1; // start on line 1
    // Loop through each line of the file
    while(getline(inFile, s))
    {
        // loop across each line one character at a time
        for(int i = 0; i<s.length(); i++)
        {
            // initialize with an empty word
            word = "";
            // if the character is alphabetical (i.e: a-z),
            // loop until a non-alphabetical character is reached
            while(isalpha(s[i]))
            {
                // add the lowercase character to the current word
                word += tolower(s[i]);
                // increment through the line
                i++;
                // note: newline characters are caught by isalpha()
                // stop once a non-alpha character
                // is reached.
            } // end while
            // if a character was added to the string, add the new word
            // to the map, along with the current line count, and reset
            // the word.
            if(word != "")
            {
                // add the word and line count, duplicates are automatically
                // handled by map and set individually
                this->data[word].insert(count);
                // reset the word
                word = "";
            } // end if
        } // end for
        count++;
    } // end while
}

/**
 * This method writes the contents of the given concordance to either
 * the standard output or the given output file.
 * @param outStream the output stream given by the user, if none was given
 * it will be the standard output
 */
void Concordance::write(ostream& outStream) const
{
    // initialize a copy of the given concordance data
    ConcordanceType data = this->data;
    // initialize iterator and set to beginning of data
    ConcordanceType::iterator it;
    it = data.begin();
    // initialize second iterator
    SetType::iterator sit;
    int count = 0;
    // iterate through the keys in the concordance map
    while(it != data.end())
    {
        // output the key with formatting
        outStream << right << setw(15) << it->first << " ";
        // initialize inner data as the 'set' value
        SetType innerData = it->second;
        // init second iterator to beginning of set
        sit = innerData.begin();
        // loop through the set in the value
        while(sit != innerData.end())
        {
            // output value with formatting
            outStream << *sit++ << " ";
            count++;
            if(count%15 == 0)
            {
                outStream << "\n" << right << setw(18);
            } // end if
        } // end while
        count = 0;
        it++;
        outStream << endl;
    } // end while
}
