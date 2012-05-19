/*****************************************************************************
* ZPanel				                                     *
*----------------------------------------------------------------------------*
* COMPILE NOTES                                                              *
* make sure to chmod +s the binary after compiling:                          *
* cc -o zsudo zsudo.c ; chmod +s zsudo                                       *
******************************************************************************/

#include <stdio.h>
#include <stdlib.h>


int main(int argc, char *argv[]) {
  if (!setuid(geteuid())) {
  char str[100];
	if(argc == 3)
  	{
  	sprintf(str,"/bin/echo '%s %s > /dev/null 2>&1' | /usr/bin/at now", argv[1], argv[2]);
	}
	if(argc == 4)
  	{
  	sprintf(str,"/bin/echo '%s %s %s > /dev/null 2>&1' | /usr/bin/at now", argv[1], argv[2], argv[3]);
	}
	if(argc == 5)
  	{
  	sprintf(str,"/bin/echo '%s %s %s %s > /dev/null 2>&1' | /usr/bin/at now", argv[1], argv[2], argv[3], argv[4]);
	}
	if(argc == 6)
  	{
  	sprintf(str,"/bin/echo '%s %s %s %s %s > /dev/null 2>&1' | /usr/bin/at now", argv[1], argv[2], argv[3], argv[4], argv[5]);
	}
  system(str);
  } else {
    printf("Couldn't set UID to effective UID\n");
    return 1;
  }
  return 0;
}
