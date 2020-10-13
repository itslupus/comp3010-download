This script was used to automatically download recorded lecture videos from the Winter 2020 offering of COMP 3010. The reason this script exists is because the videos were rotated out after each week (Monday's video will replace old Monday video, etc).  
I then became lazy. As a result that, this script was born at 4:00am in roughly 10 minutes. That probably explains why it's written in PHP.

# Requirements
I honestly have no idea what PHP version this script was written for. Lets just say you need `PHP >= 7.0` and the `curl` module.

# Running
Make sure you change the active directory in the script before you run it (first line in the script). Then execute:  
```
php -f curl.php umnetid password
```
