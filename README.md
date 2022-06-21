# PHPsheets

Create project in https://console.cloud.google.com/

Enable Google Sheets Api

Create a service account

Add key (JSON) to service account (this will download credentials.json file)

Replace credentials.json file with your credentials.json file

In the google sheet share it with client_email given in credentials.json

In working directory run this command 

composer require google/apiclient:^2.0 
