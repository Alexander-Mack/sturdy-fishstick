#include <iostream>
#include <string>
#include <cctype>

using namespace std;

int main(int argc, char *argv[])
{
    if (argc == 2)
    {
        string input = argv[1];
        string output = "";
        for (int i = 0; i < input.length(); i++)
        {
            if (i % 2 == 0)
            {
                output += toupper(input[i]);
            }
            else
            {
                output += tolower(input[i]);
            }
        }
        cout << output << endl;
    }
    else
    {
        cout << "Please provide only one string for sarcasmination." << endl;
    }
    return 0;
}