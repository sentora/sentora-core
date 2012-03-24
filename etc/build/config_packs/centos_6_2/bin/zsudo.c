/*****************************************************************************
* ZPanel -                                                                   *
* Copyright (C) Bobby Allen  & The ZPanel Development team, 2009-Present     *
* Email: ballen@zpanel.co.uk                                                 *
* Website: http://www.zpanel.co.uk                                           *
* -------------------------------------------------------------------------- *
* BY USING THIS SOFTWARE/SCRIPT OR ANY FUNCTION PROVIDED IN THE SOURCE CODE  *
* YOU AGREE THAT YOU MUST NOT DO THE FOLLOWING:-                             *
*                                                                            *
*     1) REMOVE THE COPYRIGHT INFOMATION                                     *
*     2) RE-PACKAGE AND/OR RE-BRAND THIS SOFTWARE                            *
*     3) AGREE TO THE FOLLOWING DISCLAIMER...                                *
*                                                                            *
* DISCLAIMER                                                                 *
* -------------------------------------------------------------------------- *
* THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS        *
* "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED  *
* TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR *
* PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR           *
* CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL,      *
* EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO,        *
* PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS;*
* OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY,   *
* WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR    *
* OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF     *
* ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.                                 *
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
