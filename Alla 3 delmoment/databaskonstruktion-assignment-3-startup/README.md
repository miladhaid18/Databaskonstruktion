# Dotnet Applikation

Jag har variant E (handlaggare) och jag byggde vidare mot min databas a25milha.
Jag borjade med sjalva strukturen for appen, tva controllers med varsin vy och lankar mellan dem.

# Requirements

* Eget krav: Visa startsida med lank till person-sidan.
* Eget krav: Visa person-sidan med lank tillbaka till startsidan.
* Jag visar hela Person-tabellen i en enkel html-tabell utan joins.
* Jag anropar proceduren GETAVGSAKERHET som raknar ut medelsakerhet for alla observationer.
* Eget krav: Lista alla personer med personnummer, namn och kodnamn.
* Eget krav: Visa medelsakerhet fran proceduren i en egen liten tabell.
* Jag visar observationer med join mot plats grad och agent i en nastlad tabell.
* Jag har variant E sa jag korde kolumnlayout som det stod i listan.
* Eget krav: Visa varje incident med sina observationer bredvid varandra.
* Jag kan soka personer pa namn med ett sokformular.
* Jag kan filtrera observationer per plats med en dropdown fran kodtabellen Plats.
* Eget krav: Soka fram person genom att skriva del av namnet.
* Eget krav: Valja plats i dropdown och se bara de observationerna.
* Jag kor proceduren ARKIVERAOBSERVATION med en lank per observation.
* Jag kan lagga till nya observationer med ett formular dar plats och handlaggare valjs i dropdown.
* Jag kan andra sakerhet pa en observation genom att valja den i en dropdown.
* Eget krav: Arkivera vald observation sa raden forsvinner fran tabellen.
* Eget krav: Se nya och andrade rader direkt i observationstabellen.

