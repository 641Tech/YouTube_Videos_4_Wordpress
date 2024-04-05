# YouTube_Videos_4_Wordpress
Pulls in YouTube Videos via the YouTube Data API and allows you to display them on WordPress in iframes using short codes.

# Install
To install clone the code and add your API key and channel id to the main.php file. Save the file and then zip the whole folder. In WordPress go to plugins and then add plugin. Select upload plugin and then select the .zip file.
When you activate the plugin will reachout and add the current 20 newest videos to the DB. 

# Usage
To use use the shortcode [ytvideo] to dynamically display the most recent video in you db. To select older videos use the format [ytvideo recent=0] with 0 beeing the newest and then counting up from there.