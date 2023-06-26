//node_sequence.cpp
//Mack:Alexander:A0398948:u17
//Submissions 05
//Sequences of Linked Nodes


// Everything should be to specification! I added a bit of extra input
// sanitation as the demo crashes on just about any inappropriate input.
// These include asserting argc = 7 and asserting that the inputs are of the
// correct types.

#include <cstdlib>
#include <string>
#include <fstream>
#include <iostream>
using namespace std;

#include "utilities.h"
using Scobey::Pause;
using Scobey::TextItems;
using Scobey::DisplayOpeningScreen;

typedef int DataType;
struct Node
{
    DataType data;
    Node* next;
    Node* prev;
};

/*
 * This method returns the position of a desired node in a sequece
 * return the found node or NULL
 */
Node* findNode(Node* current, int i)
{
    while(current != NULL)
    {
        if (current->data == i)
        {
            return current;
        }
        current = current->next;
    }
    return NULL;
}
/*
 * This method removes the desired node from the sequence
 * returns the location of the new head node if the head node was removed.
 */
Node* removeNode(Node* current, Node* head)
{
    if(current != NULL)
    {
        if(current->prev == NULL && current->next == NULL)
        {
            return NULL;

        }
        else if(current->next == NULL)
        {
            current = current->prev;
            current->next = NULL;
            return head;
        }
        else if(current->prev == NULL)
        {
            current = current->next;
            current->prev = NULL;
            return current;
        }
        else
        {
            current->prev->next = current->next;
            current->next->prev = current->prev;
        }
    }
    return head;
}

/*
 * This method prints the nodes in sequence from the given head node.
 */
void printNodeSequence(Node* current)
{
    while(current != NULL)
        {
            cout << current->data << " ";
            current = current->next;
        }
        cout << endl;
}

/*
 * This method adds a new node after a given node, with the specified
 * integer as content.
 */
void addNodeAfter(Node* current, int i)
{
    if(current != NULL)
    {
        if(current->next == NULL)
        {
            current->next = new Node;
            current->next->prev = current;
            current = current->next;
            current->data = i;
        }
        else
        {
            Node* tempNext = current->next;
            current->next = new Node;
            current->next->prev = current;
            current = current->next;
            current->data = i;
            current->next = tempNext;
            current->next->prev = current;
        }
    }
}

int main(int argc, char* argv[])
{
    // if no dictionary, print information screens
    if(argc == 1)

    {
        DisplayOpeningScreen("\t\tMack:Alexander:A00398948:u17",
                             "\n\t\tSubmission 05"
                             "\n\t\tSequences of Linked Nodes");
        TextItems("./node_sequence.txt").TextItems::displayItem(
            "ProgramDescription");
        return 0;
    }
    // if proper number of inputs are given, begin program
    if (argc == 7)
    {
        // initialize values from command line, if there is an error catch it
        int begin;
        int end;
        string order;
        int removed;
        int added;
        int addLoc;
        try
        {
            begin = stoi(argv[1]);
            end = stoi(argv[2]);
            order = argv[3];
            removed = stoi(argv[4]);
            added = stoi(argv[5]);
            addLoc = stoi(argv[6]);
            // Make sure that the value for order is correct, 
            // throw an error if it is not.
            if(order.compare("e") != 0 && order.compare("b") != 0)
            {
                throw invalid_argument("Invalid");
            }
        // Catch errors, print error message and terminate if caught.
        }catch(const invalid_argument& e)
        {
            cout << "Oops! Invalid input! \n"
                "Please refer to the program description.\n"
                "Program now Terminating." << endl;
            Pause();
            return 0;
        }
        // initialize node structure.
        Node* sequence = new Node;
        Node* current = sequence;
        // if appending to end of the node sequence
        if(order.compare("e") == 0)
        {
            for(int i = begin; i<end; i++)
            {
                // append contents
                current->data = i;
                // create new node
                current->next = new Node;
                // assign reverse structure
                current->next->prev = current;
                // set to new node
                current = current->next;
            }
            // set final node to last value.
            current->data = end;
        }
        else // if appending to the beginning of the node sequence
        {
            // reversed
            for(int i = begin; i<end; i++)
            {
                current->data = i;
                current->prev = new Node;
                current->prev->next = current;
                current = current->prev;
            }
            current->data = end;
            // set new head of sequence
            sequence = current;
        }
        // formatted output
        cout << "\nHere is the original sequence:\n";
        printNodeSequence(sequence);
        cout << "\nHere is the same sequence with the value " << removed <<
            "\nremoved, if the value " << removed << " was in the sequence."
            "\nOtherwise the sequence is displayed unchanged." << endl;
        // set sequence to head of node structure after removing desired node.
        // findNode will return the location of the desired node or NULL.
        sequence = removeNode(findNode(sequence, removed), sequence);
        printNodeSequence(sequence);
        cout << "\nAnd finally, here is the revised sequence with the\n"
            "value " << added << " added after " << addLoc << " if " 
            << addLoc << " was in the sequence.\n"
            "Otherwise the squence is displayed unchanged." << endl;
        // add a new Node with specified data after the desired node, 
        // if the desired node is not found, the new node will not be added.
        addNodeAfter(findNode(sequence, addLoc), added);
        printNodeSequence(sequence);
        Pause();
        return 0;
    }
    else // print error and terminate
    {
        cout << "Bad number of input parameters!\n"
            "Must be exactly 6.\n"
            "Program now terminating." << endl;
        Pause();
        return 0;
    }
}