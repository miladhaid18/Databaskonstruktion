# PHP Applikation

Kort beskrivning av applikationen
Här lägger jag text för att bekröfta ändringen.

PHP Applikation - Handläggarapplikation (Variant E) Webbapplikation i PHP för myndigheten PUCKO (Variant E: Handläggare). Applikationen är uppbyggd som en självrefererande PHP-sida som kommunicerar med databasen `a25milha`. Handläggare kan söka, registrera, visa och arkivera observationer samt hantera vittnen och rapportörer.

# Requirements

* **Självrefereerande sida:** Applikationen hanterar formulär och resultat på samma PHP-sida.
* **Enkla tabeller:** Visning av grundläggande information från databasen (t.ex. personer).
* **Lagrad procedur (Statistik):** Anrop till `CALL GETAVGSAKERHET()` för att visa medelsäkerhet.
* **Nästlade tabell (Radlayout):** Visning av observationer med incidenter, platser och handläggare i radlayout.
* **Sökning & Filtrering:** Sökformulär samt filtrering via länkar eller dropdown-menyer.
* **Databasuppdateringar (INSERT):** Formulär för att registrera nya observationer.
* **Arkivering (Procedur):** Funktion för att arkivera observationer via `CALL ARKIVERAOBSERVATION(?)`.