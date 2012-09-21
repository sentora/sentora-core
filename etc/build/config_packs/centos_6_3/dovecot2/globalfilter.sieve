require "fileinto";
  if exists "X-Spam-Flag" {
          if header :contains "X-Spam-Flag" "NO" {
          } else {
          fileinto "Spam";      
          stop;
	  }
  }
  if header :contains "subject" ["***SPAM***"] {
    fileinto "Spam";      
    stop;
  }
