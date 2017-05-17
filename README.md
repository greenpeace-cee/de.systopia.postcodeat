# de.systopia.postcodeat
CiviCRM extension for Austrian Postcodes

#### Provides autocomplete functions for Postcode, City, Street Address and State when Country=Austria.

Uses the XML zip file from Statistik Austria (http://www.statistik.at/verzeichnis/strassenliste/gemplzstr.zip)

## API Functions
PostcodeAT.Importstatistikaustria - supports auto-download from statistik austria and manual install with parameter "zipfile".
PostcodeAT.Get - main lookup function used by AJAX autocomplete.
PostcodeAT.Getatstate - lookup function for Austrian State from Postcode.
