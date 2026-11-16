# Relations
Här översätts ER-diagrammet till logiska relationer med understrukna primärnycklar och genomstrukna främmande nycklar.

**Agent**(<ins>AgentNamn, AgentNr</ins>, Fornamn, Efternamn, Lon, Kommentar)

**Handläggare**(<ins>~AgentNamn, AgentNr~</ins>)

**Incident**(<ins>IncidentId</ins>, Namn, Plats, ~RapportörNamn, RapportörNr~)

**Observation**(<ins>ObsId</ins>, Datum, Grad, Sakerhet, ~IncidentId~, ~RegistreradAvNamn, RegistreradAvNr~)

**Person**(<ins>PersonId</ins>, Namn, Kodnamn)

**Rapporterat**(<ins>~PersonId~,~ObsId~</ins>)