## Seattle Rideshare Data
### Technical Overview
The application uses Laravel (PHP) to read historical rideshare data from a MySQL database. The average and median are calculated for each point on a predetermined grid of the city. The data is then displayed as a heatmap using Google Maps (https://developers.google.com/maps/documentation/javascript/heatmaplayer).

### Application Overview
The idea behind the application was to simply analyze historical rideshare data to pinpoint ideal times and locations to drive. The application was put together fairly quickly. It was created solely for myself and was not intended for public use. Therefore, I was not concerned about caching and reducing run times. The end goal was to quickly spot trends in an easy to comprehend format with little effort.

### Lessons Learned
The biggest takeaway from this project was that datasets can get large very quickly. Even though I was only recording one data point, I made the mistake of including the coordinate pair with each data point. I should have had a separate table for the grid points and then referenced the grid point in the data table. This caused the size of the database to grow much quicker than expected. Within a month, the databse had maxed out the alloted space of 20GB.

Although this not directly tied to programming, I did learn important business lessons. I was able to spot trends and inticipate demand, but demand did not always result in increased profits. If customers were able to wait out the periods of higher prices, then fewer rides were ordered. 
