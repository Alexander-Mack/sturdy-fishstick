//NodeSequence.cpp
//Mack:Alexander:A0398948:u17
//Submissions 06
//Sequences of Linked Nodes with a NodeSequence class


// Everything should be to specification!

#include <cstdlib>
#include <string>
#include <fstream>
#include <iostream>
using namespace std;

extern const string MY_ID_INFO = "Mack:Alexander:A00398948:u17";
typedef int DataType;
struct Node
{
    DataType data;
    Node* next;
    Node* prev;
};

class NodeSequence
{
    public:
        /**
         * Create and return a sequence of nodes containing integer values,
         * including both end values of the integer range.
         * @param first    The starting value of the integer sequence.
         * @param last     The ending value of the integer sequence.
         * @param whichEnd If whichEnd == "e" add each to value to end of
         *                 sequence; if whichEnd == "b", add to beginning.
         */
        NodeSequence
        (
            int first,
            int last,
            string whichEnd
        );
        /**
         * Display the values in the sequence, all on the same line,
         * separated by a blank space, and then terminate the line.
         */
        void display();

        /**
         * Remove a value from the sequence, if it is in the sequence.
         * If the value is not in the sequence, leave the sequence unchanged.
         * @param valueToRemove The value to be removed.
         */
        void remove
        (
            int valueToRemove
        );

        /**
         * Insert a value to the sequence after a given value, if the given
         * value is actually in the sequence. If the given value is not in
         * the sequence, leave the sequence unchanged.
         * @param valueToInsert The value to be inserted.
         * @param valueToInsertAfter The value after which valueToInsert is
         *                           to be inserted.
         */
        void insert
        (
            int valueToInsert,
            int valueToInsertAfter
        );

        /**
         * Search for the desired value within the sequence. If the value is
         * found inside the sequence, then return the node's location.
         * Otherwise return a NULL value to state that the data had no match. 
         * @param data The value to find within the sequence.
         */
        Node* findNode
        (
            int data
        );

    private:
        Node* head;
        Node* tail;
};

NodeSequence::NodeSequence(int first, int last, string whichEnd)
{
    // initialize node structure.
    this->head = new Node();
    Node* current = this->head;
    // if appending to end of the node sequence
    if(whichEnd.compare("e") == 0)
    {
        for(int i = first; i<last; i++)
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
        current->data = last;
    }
    else // if appending to the beginning of the node sequence
    {
        // reversed
        for(int i = first; i<last; i++)
        {
            current->data = i;
            current->prev = new Node;
            current->prev->next = current;
            current = current->prev;
        }
        current->data = last;
        // set new head of sequence
        this->head = current;
    }
}

void NodeSequence::display()
{
    Node* current = this->head;
    // loop through the sequence printing the data of each
    while(current != NULL)
    {
        cout << current->data << " ";
        current = current->next;    
    }
    cout << endl;
}

void NodeSequence::remove(int valueToRemove)
{
    // check the sequence for the value
    Node* current = findNode(valueToRemove);
    // if the node is found
    if(current != NULL)
    {
        // if it is the only node in the sequence
        if(current->prev == NULL && current->next == NULL)
        {
            // set the sequence to NULL
            this->head = NULL;
        }
        // if it is the last node in the sequence
        else if(current->next == NULL)
        {
            // move to previous node and remove the desired node
            current = current->prev;
            current->next = NULL;
        }
        // if it is the first node in the sequence
        else if(current->prev == NULL)
        {
            // move to next node and remove the desired node
            current = current->next;
            current->prev = NULL;
            // set new head of sequence
            this->head = current;
        }
        // if it is in the sequence
        else
        {
            // set the 'next' value of the previous node to the next node
            // in the sequence after the removed node and vice versa
            current->prev->next = current->next;
            current->next->prev = current->prev;
        }
    }
}

void NodeSequence::insert(int valueToInsert, int valueToInsertAfter)
{
    // check the sequence for the value
    Node* current = findNode(valueToInsertAfter);
    // if the node is found
    if(current != NULL)
    {
        // if the node is the last one in the sequence
        if(current->next == NULL)
        {
            // create a new node and set the data value to the desired value
            current->next = new Node;
            current->next->prev = current;
            current = current->next;
            current->data = valueToInsert;
        }
        // if the node is in the sequence
        else
        {
            // create a new node and adjust the linkage to accomodate
            Node* tempNext = current->next;
            current->next = new Node;
            // set reverse linkage for new value
            current->next->prev = current;
            current = current->next;
            // set data value
            current->data = valueToInsert;
            // set linkage
            current->next = tempNext;
            // set reverse linkage of next value
            current->next->prev = current;
        }
    }
}

Node* NodeSequence::findNode(int data)
{
    Node* current = this->head;
    // iterate through the sequence
    while(current != NULL)
    {
        // if the data matches the desired value
        if (current->data == data)
        {
            // return the current node
            return current;
        }
        current = current->next;
    }
    // return NULL if not found
    return NULL;
}