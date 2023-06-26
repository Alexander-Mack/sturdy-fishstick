// dining_philosophers.cpp
// Assignment 05, Question 05
// Alexander Mack
// A00398948

#include <cstdlib>
#include <stdio.h>
#include <pthread.h>
#include <unistd.h>
#include <mutex>
#include <iostream>

using namespace std;
const int NUM_PHILOSOPHERS = 5;
int HUNGRY = 0;
int EATING = 1;
int THINKING = 2;
int S[NUM_PHILOSOPHERS];
std::mutex mtx;
int state[NUM_PHILOSOPHERS];

void down(int *S)
{
    while(*S <= 0);
    *S--;
}

void up(int *S)
{
    *S++;
}

void test(int i)
{
    if(state[i] == HUNGRY && 
    state[(i-1)%NUM_PHILOSOPHERS] != EATING &&
    state[(i+1)%NUM_PHILOSOPHERS] != EATING)
    {
        state[i] = EATING;
        cout << "P" << i << " is eating ..." << endl;
        up(&S[i]);
    }
}
void take_forks(int i)
{
    mtx.lock();
    state[i] = HUNGRY;
    cout << "P" << i << " is hungry ..." << endl;
    test(i);
    mtx.unlock();
    down(&S[i]);
}

void put_forks(int i)
{
    mtx.lock();
    state[i] = THINKING;
    test((i-1)%NUM_PHILOSOPHERS); // needs modulus operator
    test((i+1)%NUM_PHILOSOPHERS); // needs modulus operator
    mtx.unlock();
}
void *philosopher(void* arg)
{
    int i = (int*)arg;
    while(true)
    {
        state[i] = THINKING;
        cout << "P" << i << " is thinking ..." << endl;
        sleep(rand()%10);
        take_forks(i);
        sleep(rand()%10);
        put_forks(i);
    }
}

int main()
{
    pthread_t thread[NUM_PHILOSOPHERS];
    for(int i = 0; i<NUM_PHILOSOPHERS; i++)
    {
        S[i] = 0;
        pthread_create(&thread[i], NULL, &philosopher, (void*) i);
    }
}