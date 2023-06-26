//eval_pre_post.cpp
//Mack:Alexander:u17:A00398948
//Submission 10
//Evaluating Prefix and Postfix Expressions

//#include whatever header files you need here
#include <iostream>
#include <string>
#include <stack>
#include <cctype>
using namespace std;

#include "utilities.h"
using Scobey::DisplayOpeningScreen;
using Scobey::Pause;


//If you want to use the (suggested) Exception class, put it here.
class Exception
{
public:
    Exception(const string& message) {this->message = message;}
    string getMessage() {return message;}
private:
    string message;
};

/**
 * Display a single screen of program information, with a pause at the end.
 */
void DisplayUsage()
{
    cout << "This program evaluates any valid prefix or postfix "
        "expression which contains\npositive integer operands and the "
        "operators +, -, * and/or /. The expression\nto be evaluated "
        "must be entered on the command line within double quotes."
        "\nNote that a single positive integer simply evaluates as itself."

        "\n\nTypical usage examples:"
        "\n> eval_pre_post + * 2 3 4"
        "\n+ * 2 3 4 = 10"
        "\n> eval_pre_post 2 3 4 + *"
        "\n2 3 4 + * = 14"

        "\n\nThe following errors are recognized and reported "
        "for prefix expressions:"
        "\nError: End of prefix expression reached unexpectedly."
        "\nError: Bad operator ? encountered evaluating prefix expression."
        "\nError: Extraneous characters found after valid prefix expression."
        "\n\nThe following errors are recognized and reported "
        "for postfix expressions:"
        "\nError: End of postfix expression reached unexpectedly."
        "\nError: Bad operator ? encountered evaluating postfix expression."
        "\nError: Not enough operands for postfix operator ?."
        "\nError: Found too many operands when evaluating postfix expression."
        "\n\n           "
        "                                                    Screen 1 of 1\n";
    Pause();
}

/**
 * This function checks the given character to see if it is an operator.
 * @param input the character to check.
 */
bool isOperator(char input)
{
    return input == '+' || input == '-' || input == '*' || input == '/';
}

/**
 * This function calculates the result of a postfix expression. It will
 * assume that the function is definitely a postfix expression, and then
 * push all operators and operands to seperate stacks. As the stacks build
 * up, the function will perform calculations on the top members, and then
 * add the result to the stack.
 * @param input the user input string given to the function
 * @param i the index of the input string to begin
 */
int postfixCalc(string input, int i)
{
    // initialize variables
    int size = input.length();
    //bool opFlag = false;
    stack<int> operands; //operand stack
    stack<char> operators; // operator stack
    string error;
    // loop through the input string
    for(i=i;i<size;i++)
    {
        if(isblank(input[i])); // if blank do nothing, allow nothing
        else if (isdigit(input[i])) // if digit, get the full integer
        {
            string temp = "";
            while(isdigit(input[i]))
            {
                temp += input[i];
                i++;
            }
            operands.push(stoi(temp)); // push integer to stack
        }
        else if (isOperator(input[i])) // if operator
        {
            
            //opFlag = true;
            operators.push(input[i]); // push operator to stack
        }
        else // if neither operator or integer, error
        {
            error = "Bad operator ";
            error.push_back(input[i]);
            error += " encountered evaluating postfix expression.";
            throw Exception(error); // error is caught in main
        }
        // once the stacks have more than two operands and a operator
        if(operands.size()>=2 && operators.size()>=1)
        {
            // set and pop the top values of the stacks
            int second = operands.top();
            operands.pop();
            int first = operands.top();
            operands.pop();
            char op = operators.top();
            operators.pop();
            int sum;
            switch(op)
            {
                case '+':
                    sum = first + second;
                    break;
                case '-':
                    sum = first - second;
                    break;
                case '*':
                    sum = first * second;
                    break;
                case '/':
                    sum = first / second;
                    break;
            }
            // push new value to stack
            operands.push(sum);
        }
    }// end of input loop
    // if 2 operands are still in the stack, and the last value was an operator
    if(operands.size()>1 && isOperator(input[size-1]))
    {
        throw Exception("Found too many operands when evaluating "
            "postfix expression.");
    }
    // if 2 operands are still in the stack and the last value was an operand
    if(operands.size()>1 && isdigit(input[size-1]))
    {
        throw Exception("End of postfix expression reached unexpectedly.");
    }
    // if there are still operators in the stack
    if(operators.size() > 0)
    {
        while(operators.size() > 1)
        {
            operators.pop(); // get the first operator
        }
        error = "Not enough operands for postfix operator ";
        error.push_back(operators.top());
        error += ".";
        throw Exception(error);
    }
    // return the last value at the top of the operand stack
    return operands.top();
}

/**
 * This function calculates the result of a prefix expression. It will
 * assume that the function is definitely a prefix expression, and then
 * push all operators and operands to seperate stacks. As the stacks build
 * up, the function will perform calculations on the top members, and then
 * add the result to the stack. If extra operators are found after reaching
 * operands, recurse from that point and return the value to the stack
 * @param input the user input string given to the function
 * @param i the index of the input string to begin
 */
int prefixCalc(string input, int i)
{
    // init vars
    string error;
    int size = input.length();
    bool opFlag = false; // flag to check if operators are encountered after 
                         // encountering operands
    bool recFlag = false; // flag to announce that the program is recursing
                          // to prevent duplication
    stack<int> operands; // operand stack
    stack<char> operators; // operator stack
    // loop through the input
    for(i=i;i<size;i++)
    {
        if(isblank(input[i]) || recFlag); // if blank or recursing, do nothing
        else if(isdigit(input[i])) // if digit, get full integer
        {
            int begin = i;
            string temp = "";
            while(isdigit(input[i]))
            {
                temp += input[i];
                i++;
            }
            operands.push(stoi(temp)); // push to stack
            opFlag = true; // set opFlag
        }
        else if(isOperator(input[i])) // if operator, push or recurse
        {
            if(opFlag) // if flag is set, recurse
            {
                recFlag = true; // set recFlag
                operands.push(prefixCalc(input, i)); // push recurse to stack
            }
            else
            {
                operators.push(input[i]); // push to stack
            }
        }
        else // if neither operator or integer
        {
            error = "Bad operator ";
            error.push_back(input[i]);
            error += " encountered evaluating prefix expression.";
            throw Exception(error); // exception caught in main
        }
        // if the stacks have 2 operands and an operator, calculate
        if(operands.size()>=2 && operators.size()>=1)
        {
            int second = operands.top();
            operands.pop();
            int first = operands.top();
            operands.pop();
            char op = operators.top();
            operators.pop();
            int sum;
            switch(op)
            {
                case '+':
                    sum = first + second;
                    break;
                case '-':
                    sum = first - second;
                    break;
                case '*':
                    sum = first * second;
                    break;
                case '/':
                    sum = first / second;
                    break;
            }
            
            operands.push(sum); // push calculation to stack
        }
    } // end of input loop
    if(operators.size()>0) // if there are operators left in the stack
    {
        throw Exception("End of prefix expression reached unexpectedly.");
    }
    if(operands.size() > 1) // if there is more than 1 operand left
    {
        throw Exception("Extraneous characters found "
            "after valid prefix expression.");
    }
    // return last operand left at the top of the stack
    return operands.top();
}

/**
 * The main program, it takes user input and checks if it is a prefix
 * or postfix notation, then outputs it with the solution to the user.
 */
int main(int argc, char* argv[])
{
    if (argc == 1)
    {
        Scobey::DisplayOpeningScreen("\t\tMack:Alexander:u17:A00398948",
            "\n\t\tSubmission 10"
            "\n\t\tEvaluating Prefix and Postfix Expressions");
        DisplayUsage();
        return EXIT_SUCCESS; //Just a name for 0
    }
    //Put the rest of your main() driver code here.
    bool prefix = true; // true if prefix notation, false if postfix
    string input = argv[1];
    int i = 0, size = input.length();
    // find the first character that is not blank to decide
    // if the expression is prefix or postfix
    while(isblank(input[i]))
    {
        i++;
    }
    if(isdigit(input[i]))// if first char is digit, postfix
    {
        prefix = false;
    }
    else if(input[i] == '+' || 
        input[i] == '-' || 
        input[i] == '*' || 
        input[i] == '/') // if first char is operator, prefix
    {
        prefix = true;
    }
    int sum;
    try{
        if(prefix)
        {
            sum = prefixCalc(input, i);
        }
        else
        {
            sum = postfixCalc(input, i);
        }
    }
    catch(Exception e) // catch errors from the functions
    { // print error and close
        cout << "Error: " << e.getMessage() << endl;
        cout << "Program is now terminating." << endl;
        Pause();
        return 0;
    }
    // print input alongside the solution
    cout << input << " = " << sum << endl;

    return 0;
}

