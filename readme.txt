Processes USGS water data.  Specifically, this early version gets the water flow rate, level, and temperature at the bottom of Buford Dam 
which creates Lake Lanier in Georgia, US.  USGS = United States Geological Survey, https://www.usgs.gov/

The general output I am heading towards is conceptually the same as this:

CHATTAHOOCHEE RIVER AT BUFORD DAM, NEAR BUFORD, GA Temperature, water, degrees Celsius 9.6 2020-02-15T23:30:00.000-05:00
CHATTAHOOCHEE RIVER AT BUFORD DAM, NEAR BUFORD, GA Discharge, cubic feet per second 10900 2020-02-15T23:30:00.000-05:00
CHATTAHOOCHEE RIVER AT BUFORD DAM, NEAR BUFORD, GA Gage height, feet 5.23 2020-02-15T23:30:00.000-05:00
*****
lest I tattoo this:

git remote set-url origin git@github.com:kwynncom/usgs-water-services-processing.git

**********

arch/w1.php is the very basic program.  See the sample output.  I blocked it against anything but command line (cli) because it gives 
unrestricted accesss to https://waterservices.usgs.gov/nwis/iv/?format=json&sites=02334430  See USGS_output.txt.

w2.php is an example of my occasionally demented thinking: making things harder rather than easier
A version of kwutils.php is in my other GitHub projects.  Eventually I will likely hard-link it to this one.


As best I remember, w3 is pretty close to what I'll post as water.php

water.html has some relevant links.

water.php takes the USGS input and outputs to 2 MongoDB tables / collections.  I give sample output further below.

water.php enforces a quota of one call to USGS per 30 minutes.  


SAMPLE MONGODB OUTPUT:

water collection:

{
    "_id" : ObjectId("5e4af472eeeeea27c51fa022"),
    "json" : "{\"name\":\"ns1:timeSeriesResponseType\",[...]",
    "callts" : 1581970546,
    "callElapsed" : 0.596867084503174,
    "C" : 9.8,
    "F" : 50,
    "cfs" : "10800",
    "datv" : 5,
    "ft" : "5.16",
    "obsats" : "2020-02-17T15:00:00.000-05:00",
    "obsatts" : 1581969600,
    "siteName" : "ChattDam"
}

sites collection:

{
    "_id" : ObjectId("5e48c1bc02b8943682a7938f"),
    "siteCode" : "02334430",
    "lat" : 34.15694444,
    "lon" : -84.07888889,
    "myname" : "ChattDam",
    "oname" : "CHATTAHOOCHEE RIVER AT BUFORD DAM, NEAR BUFORD, GA"
}
