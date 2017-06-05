# de.systopia.postcodeat
CiviCRM extension for Austrian Postcodes

#### Provides autocomplete functions for Postcode, City, Street Address and State when Country=Austria.
#### Rearranges address fields so ZIP Code and City are displayed at the top.

Uses the XML zip file from Statistik Austria (http://www.statistik.at/verzeichnis/strassenliste/gemplzstr.zip)

## API Functions
PostcodeAT.Importstatistikaustria - supports auto-download from statistik austria and manual install with parameter "zipfile".
PostcodeAT.Get - main lookup function used by AJAX autocomplete.
PostcodeAT.Getatstate - lookup function for Austrian State from Postcode.

## Installation / Usage
**This extension will not look up any addresses until you execute the API function PostcodeAT.Importstatistikaustria and it has completed successfully.**
Normally this will retrieve all data automatically but if you do not have an internet connection on your server you will have to download the zip file manually and specify it's location using the parameter "zipfile" when you run the API function.
