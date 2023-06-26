// CSCI3431-Mack.c
// Alexander Mack
// A00398948
// Project 2

#include <semaphore.h>
#include <stdio.h>
#include <stdlib.h>
#include <unistd.h>
#include <pthread.h>
#include <string.h>
#include <ctype.h>
#define REQUESTED 1
#define ACQUIRED 2
#define RELEASED 0
int bus_line[4] = {0}; // each line of buses
sem_t north, south, east, west; // semaphores for each direction
sem_t crossing; // semaphore for crossing the road
int matrix[4][4]; // local storage of the bus matrix
int complete = 0;
pthread_t thread[4];
FILE *matFile;
int bus_count = 1;
int current_bus[4];


char* getDirection(int dir)
{
    char* direction;
    switch(dir)
    {
        case 0:
            direction = "north";
            break;
        case 1:
            direction = "west";
            break;
        case 2:
            direction = "south";
            break;
        case 3:
            direction = "east";
            break;
    }
    return direction;
}

/**
 * This function updates the matrix file with the updated version of the
 * local matrix
 */
void matFile_update()
{
    // move file pointer to beginning of file
    fseek(matFile, 0, SEEK_SET);
    fprintf(matFile, "%c %d %d %d %d\n", 'n', 
        matrix[0][0], matrix[0][1], matrix[0][2], matrix[0][3]);
    fprintf(matFile, "%c %d %d %d %d\n", 'w', 
        matrix[1][0], matrix[1][1], matrix[1][2], matrix[1][3]);
    fprintf(matFile, "%c %d %d %d %d\n", 's', 
        matrix[2][0], matrix[2][1], matrix[2][2], matrix[2][3]);
    fprintf(matFile, "%c %d %d %d %d\n", 'e', 
        matrix[3][0], matrix[3][1], matrix[3][2], matrix[3][3]);
}

/**
 * This function takes the bus direction and the direction of the semaphore
 * that are being changed, and updates their information in the matrix
 * according to the desired state.
 * @param dir the direction of the semaphore
 * @param bus_dir the direction of the bus collecting the semaphore
 * @param state the new state of the bus
 */
void mat_write(int dir, int bus_dir, int state)
{
    for(int i = 0; i<4; i++)
    {
        for(int j = 0; j<4; j++)
        {
            if(i == bus_dir && j == dir)
            {
                matrix[i][j] = state;
            }
        }
    }
    matFile_update(); // update the matrix.txt
}

/**
 * This function acquires the crossing matrix
 */
void mat_acquire()
{
    sem_wait(&crossing);
}

/**
 * This function releases the crossing matrix
 */
void mat_release()
{
    sem_post(&crossing);
}

/**
 * This function acquires the directional semaphore accordingly
 * @param dir the direction of the semaphore desired
 * @param bus_dir the direction of the bus acquiring the semaphore
 * @param cur the current bus being managed
 */
void acquire(int dir, int bus_dir, int cur)
{
    // update matrix to REQUESTED
    mat_acquire();
    printf("Bus %d from the %s wants to acquire the %s lock ...\n", cur,
                getDirection(bus_dir), getDirection(dir));
    mat_write(dir, bus_dir, REQUESTED);
    mat_release();
    switch(dir){
        case 0:
            sem_wait(&north);
            break;
        case 1:
            sem_wait(&west);
            break;
        case 2:
            sem_wait(&south);
            break;
        case 3:
            sem_wait(&east);
            break;
    }
    // update matrix to ACQUIRED
    mat_acquire();
    printf("Bus %d from the %s has acquired the %s lock ...\n", cur,
                getDirection(bus_dir), getDirection(dir));
    mat_write(dir, bus_dir, ACQUIRED);
    mat_release();
}

/**
 * This function releases the directional semaphore accordingly
 * @param dir the direction of the semaphore requested
 * @param bus_dir the directionof the bus requesting the lock
 * @param cur the current bus being managed
 */
void release(int dir, int bus_dir, int cur)
{
    // update the matrix to RELEASED
    mat_acquire();
    printf("Bus %d from the %s has released the %s lock ...\n", cur,
                getDirection(bus_dir), getDirection(dir));
    mat_write(dir, bus_dir, RELEASED);
    mat_release();
    switch(dir){
        case 0:
            sem_post(&north);
            break;
        case 1:
            sem_post(&west);
            break;
        case 2:
            sem_post(&south);
            break;
        case 3:
            sem_post(&east);
            break;
    }
}

/**
 * This function is called by the threads to simulate bus lines.
 * it checks to see if there is a bus in the queue, and then tries
 * to acquire the semaphore of its own direction, then it tries to
 * acquire the semaphore of the direction to its right.
 * @param arg the direction of the buses
 */
void *bus(void* arg)
{
    int dir = (int*)arg;
    char* direction = getDirection(dir);
    printf("Created the %s bus line ...\n", direction);
    while(complete != 1)
    {
        while(bus_line[dir] > 0)
        {
            current_bus[dir] = bus_count++;
            printf("Bus %d has arrived from the %s ...\n", current_bus[dir], direction);
            acquire(dir, dir, current_bus[dir]);
            acquire((dir+1)%4, dir, current_bus[dir]);
            printf("The %s bus number %d is crossing ...\n", direction, current_bus[dir]);
            mat_acquire();
            sleep(2);
            bus_line[dir]--;
            printf("Bus %d from the %s has crossed and is releasing the locks ...\n", 
                current_bus[dir], direction);
            mat_release();
            release((dir+1)%4, dir, current_bus[dir]);
            release(dir, dir, current_bus[dir]);
        }
    }
}
/**
 * This function checks to see if the matrix is currently in a deadlock
 * condition.
 * @return 0 if it is not in a deadlock, 1 if it is currently deadlocked.
 */
int dead_cond()
{
    int dead;
    // if deadlock conditions are true for all active buses
    for(int i = 0; i< 4; i++)
    {
        // if the direction and the direction to the right are 
        // ACQUIRED and REQUESTED specifically, set dead to 1
        if(matrix[i][i] == ACQUIRED && matrix[i][(i+1)%4] == REQUESTED)
        {
            dead = 1;
        }
        else // if not set dead to 0 and break loop
        {
            dead = 0;
            break;
        }
    }
    return dead;
}

/**
 * This function reads the matrix.txt file to see if the bus matrix
 * is currently in deadlock conditions.
 * @return whether the system is currently in deadlock conditions
 */
int checkForDeadLock()
{
    // move pointer to beginning of file
    fseek(matFile, 0, SEEK_SET);
    char dir;
    int mat[4];
    int i = 0;
    while((fscanf(matFile, "%c %d %d %d %d", &dir,
        &mat[0], &mat[1], &mat[2], &mat[3])) == 1)
    {
        for(int j = 0; j<4;j++)
        {
            matrix[i][j] = mat[j];
        }
        
        i++;
    }
    return dead_cond();
}

int main(int argc, char* argv[])
{
    // if incorrect arguments are given, print info and close
    if(argc != 3)
    {
        printf("Please enter a input file and a decimal value between "
                "0.2 and 0.7\n");
        return 0;
    }
    // init variables and semaphores, all shared, all available to begin
    char* inFileName = argv[1];
    float prob = atof(argv[2]);
    sem_init(&north, 0, 1);
    sem_init(&west, 0, 1);
    sem_init(&south, 0, 1);
    sem_init(&east, 0, 1);
    sem_init(&crossing, 0, 1);
    FILE *inFile = fopen(inFileName, "r");
    matFile = fopen("matrix.txt", "w+");
    if(!inFile)
    {
        printf("Oops! Something went wrong with opening %s!\n", inFileName);
        return 0;
    }
    if(!matFile)
    {
        printf("Oops! Something went wrong with opening matrix.txt!\n");
    }
    // create empty matrix
    matFile_update();
    // create bus lines for the intersection
    for(int i = 0; i<4; i++)
    {
        pthread_create(&thread[i], NULL, &bus, (void*)i);
    }
    char next;
    // loop through the input file to add the next bus 
    // and/or check for a deadlock
    while(!complete)
    {
        float random = (float)(rand()%10)/10; // random value from 0.0 to 0.9
        if(random >= prob) // 1-p probability -> add bus
        {
            next = fgetc(inFile);
            switch(tolower(next))
            {
                case 'n':
                    bus_line[0]++;
                    break;
                case 's':
                    bus_line[1]++;
                    break;
                case 'e':
                    bus_line[2]++;
                    break;
                case 'w':
                    bus_line[3]++;
                    break;
                default: // this will occur when the loop reaches EOF
                    complete = 1;
                    break;
            }
        }
        if(random < prob) // p probability -> check for deadlock
        {
            if(checkForDeadLock())
            {
                // output cycle and close
                printf("System Deadlocked\n");
                for(int i = 0; i<4; i++)
                {
                    printf("Bus %d from the %s is waiting on bus "
                    "%d from the %s ...\n", current_bus[i], 
                    getDirection(i), current_bus[(i+1)%4], 
                    getDirection((i+1)%4));
                }
                return 0;
            }
        }
    }
    // loop until buses are all passed
    while(bus_line[0] > 0 || bus_line[1] > 0 
        || bus_line[2] > 0 || bus_line[3] > 0)
    {
        // check for deadlock once per second
        if(checkForDeadLock())
        {
            // output cycle and close
            printf("System Deadlocked\n");
            for(int i = 0; i<4; i++)
            {
                printf("Bus %d from the %s is waiting on bus "
                    "%d from the %s ...\n", current_bus[i], 
                    getDirection(i), current_bus[(i+1)%4], 
                    getDirection((i+1)%4));
            }
            return 0;
        }
        sleep(1);
    }
    // once all the lines are empty
    printf("Lines are cleared, finishing up ... \n");
    // join all threads
    for(int i = 0; i<4; i++)
    {
        pthread_join(thread[i], NULL);
    }
    fclose(matFile);
    fclose(inFile);
}