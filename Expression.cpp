//Expression.cpp
//Mack:Alexander:A00398948:u17
//Submission 09
//Building and Evaluating Expression Trees

// Everything should be to specifications!

#include "Expression.h"
#include <cctype>
// TreeNode eTree;
// bool eTreeValid;

/**
 * This method creates an expression tree based on the given string input.
 * if there is no operator character in the string, it will attempt to parse
 * the string into an integer and set it to intValue.
 * If there is an operator character in the string, it will attempt to create
 * two new trees, one to the left and one to the right, without the
 * surrounding brackets.
 * @param input the string input by the user, or some substring of it.
 */
TreeNode* createTree(string input)
{
    // init variables
    TreeNode* tree = new TreeNode();
    int b_count = 0;
    int size = input.length();
    bool op_found = false;
    // loop through looking for brackets and operator characters
    for(int i = 0; i<size; i++)
    {
        // increment for each open bracket
        if(input[i] == '(')
        {
            b_count++;
        }
        // decrement for each closing bracket
        else if (input[i] == ')')
        {
            b_count--;
        }
        // if an operator character is found, and it is the "middle"
        if((input[i] == '+' ||
            input[i] == '-' ||
            input[i] == '*' ||
            input[i] == '/') &&
            b_count == 1)
        {
            // set boolean
            op_found = true;
            // set tree values
            tree->tag = SUB_NODE;
            tree->op = input[i];
            // recurse for left and right nodes
            tree->left = createTree(input.substr(1,i-1));
            tree->right = createTree(input.substr(i+1, size - i - 2));
            // exit loop
            break;
        }
    }
    // if no operators are found in the string (i.e. an integer)
    if(!op_found)
    {
        // set tree values
        tree->tag = INT_ONLY;
        for(int i = 0; i<size; i++)
        {
            if(!isdigit(input[i]))
            {
                throw runtime_error("error");
            }
        }
        tree->intValue = stoi(input);
        // return the node
        return tree;
    }
    // return the head node
    return tree;
}

/** 
 * This method is the main constructor for the Expression class, it creates
 * a TreeNode object via an input string. It removes the spaces from the 
 * string, then attempts to create the tree from the cleaned string. If it
 * encounters an error, it sets the tree as invalid, otherwise if it
 * completes, it will set the expression as valid.
 * @param inStream the input stream given by the user for the expression tree
 */
Expression::Expression(istringstream& inStream)
{
    // copy stream to a string
    string input = inStream.str();
    string treeString = "";
    // loop through removing spaces
    for(char c: input)
    {
        if(!isspace(c))
        {
            treeString += c;
        }
    }
    // try to create the tree
    try{
        this->eTree = *createTree(treeString);
        this->eTreeValid = true;
    }
    // catch any errors that may occur
    catch(const exception e)
    {
        this->eTreeValid = false;
    }
}

/**
 * This method returns whether the expression is valid
 */
bool Expression::isValid()
{
    return this->eTreeValid;
}

/**
 * This method calculates the value of the tree based on the given
 * operator characters in the tree. It recurses through the tree until
 * it reaches an integer node, and then returns it back through.
 * @param node the head node of the tree
 */
int calculateTree(TreeNode node)
{
    int sum = 0;
    // if the node is an integer node, return the value
    if(node.tag == INT_ONLY)
    {
        return node.intValue;
    }
    // else if the node is a sub node, make calculation through recursion
    if(node.tag == SUB_NODE)
    {
        switch(node.op)
        {
            case '+':
                sum = calculateTree(*node.left) 
                    + calculateTree(*node.right);
                break;
            case '-':
                sum = calculateTree(*node.left) 
                    - calculateTree(*node.right);
                break;
            case '*':
                sum = calculateTree(*node.left) 
                    * calculateTree(*node.right);
                break;
            case '/':
                sum = calculateTree(*node.left) 
                    / calculateTree(*node.right); 
                break;
        }
        return sum;
    }
    return 0;
}

/**
 * This method returns the value of the given expression object. It calls 
 * calculateTree() to calculate the tree.
 */
int Expression::getValue()
{
    return calculateTree(this->eTree);
}

/**
 * This method converts a tree into a string that can be output to the user.
 * It recurses through the tree until it reaches the integer nodes, and then
 * formats them appropriately
 * @param output the tree to be output to the user.
 */
string TreeToString(TreeNode output)
{
    // if output node is an integer, return integer
    if(output.tag == INT_ONLY)
    {
        return to_string(output.intValue);
    }
    // if output node is a tree, return the left and right side with the
    // operator in the middle
    if(output.tag == SUB_NODE)
    {
        return "(" + TreeToString(*output.left) + " " + output.op
            + " " + TreeToString(*output.right) + ")";
    }
    return "";
}

/**
 * This method creates and outputs the given tree object to the user.
 * it calls TreeToString() to create the output string.
 */
void Expression::display()
{
    string output = TreeToString(this->eTree);
    cout << output;
}

