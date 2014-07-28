/*****************************************************************************
* ZPanel *
*----------------------------------------------------------------------------*
* COMPILE NOTES *
* make sure to chmod +s the binary after compiling: *
* cc -o zsudo zsudo.c ; chmod +s zsudo *
*
*
* [*] 1/Feb/2014 -> (Japp from 0xlabs. japp[@]0xlabs.com) :
* - Command Injection patched.
* - Buffer Overflow patched.
* - Added dynamic number of arguments (MAX_ARGS)
*
*
*
*
*
******************************************************************************/

#include <stdio.h>
#include <string.h>
#include <stdlib.h>

#define MAX_ARGS 11 // Max. arguments : argv[0] + 10
#define MIN_LEN 47 // strlen ("/bin/echo ' > /dev/null 2>&1' | /usr/bin/at now")

int ParamsLen (int argc, char **argv)
{
        int i;
        int len = 0;
        
        for ( i = 0; i < argc; i++ )
                len += strlen(argv[i]) + 1; // 1 == ' '
        
        return len;
}

int EscapeArgs (char *parsed, int argc, char **argv)
{
        int i, a, j = 0;
        
        for ( i = 0; i < argc; i++, j++ )
        {
                for ( a = 0; a < strlen(argv[i]); a++ )
                {
                        if ( argv[i][a] != '\'' )
                        {
                                parsed[j] = argv[i][a];
                                j++;
                        }
                }
                parsed[j] = ' ';
        }
        
        parsed[j] = 0x0;
        
        return j;
                
}

void MemError ( void )
{
        perror ("Error allocating memory:");
        exit (-1);
}

int main(int argc, char *argv[])
{
        if ( argc < 2 )
        {
                printf ("Argument needed\n");
                return 1;
        }
        else if ( argc > MAX_ARGS )
        {
                printf ("Too much arguments\n");
                return 1;
        }
        else
        {
         
                if ( !setuid( geteuid() ) )
                {
                        char *parsed = NULL;
                        char *str = NULL;
                        int total_len = 0;
                        
                        total_len = ParamsLen ((argc-1), &argv[1]);
                                
                        parsed = (char *) malloc ( total_len + 1 );
                        if ( parsed == NULL ) MemError();
                        
                        total_len = EscapeArgs (parsed, argc-1, &argv[1]);
                        
                        total_len += MIN_LEN + 1;
                        
                        str = (char *) malloc ( total_len );
                        if ( str == NULL ) MemError();
                        
                        snprintf (str, total_len, "/bin/echo '%s > /dev/null 2>&1' | /usr/bin/at now", parsed);
                                        
                        system(str);
                        
                        free (parsed);
                        free (str);
                }
                else
                {
                            printf("Couldn't set UID to effective UID\n");
                            return 1;
                }
        }
        return 0;
}
