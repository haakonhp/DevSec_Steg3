# Gruppe 1

## Gruppemedlemmer
#### Ole Marcus Løve Hansen - omhanse@hiof.no
#### Håkon Halland Hovland Pedersen - haakonp@hiof.no
#### Anel Hadzic - anelh@hiof.no
#### Alexander Ombudstvedt Zarkoob - alexander.o.zarkoob@hiof.no
#### Peter Johannes Brännström - peter.j.brannstrom@hiof.no
#### Jarle Syvertsen - jarle.syvertsen@hiof.no
#### Andreas Sebastian Salomonsen Thorbjørnsen - andreas.s.thorbjornsen@hiof.no

## Overordnede verktøy
Prosjektet er som definert i oppgaven bygd på en blanding av en MySQL server
som database hvor informasjonen leveres og styes av klienten via PHP. Primært benyttes
PHP sin MySQLi-funksjon til kommunikasjon mellom de to punktene, og funksjonspunktene
til databasen fungerer gjennom flere og spesifikt definerte databasekall (CALL).


## Hvordan kravspesifikasjonen tolkes, og implementasjoner.
### En student skal:
#### Kunne registrere seg
Det er mulig for en student å registrere seg i student_signup.php. Her vil studenten
få muliige valg av studieretning og studiekull som hentes fra datbasen, og kombinasjonen
av disse to utgir hvilke emner studenten har tilgang til. Studentens informasjon sendes og
lagres i databasen ved registrering.

#### Kunne logge inn
Alle logins, uansett rolle i systemet, hånteres av login.php. Ved login vil bruker bli "tildelt" en rolle i session
som både garanterer og begrenser brukerens tilgang til respektive sider.

#### Bytte/Glemt passord
Glemt passord-funksjonen initialiseres av reset-password.php og benytter mailfunksonen i
PHP (gjennom PHPMailer) til å sende en gyldig token til brukerens email for muligheten til
å opprette et nytt passord. Bytte av passord kan innloggede bruker gjøre i byttpassord.php som
referes til fra index-siden. Her bruker vi password_verify for å sjekke opp mot brukerens hashede passord.
Disse funksjonene fungerer likt for alle brukergrupper.

#### Kunne sende meldinger angående ønsked fag, men forbli annonym
Dette er noe vi løser i emne.php hvor alle brukergrupper som har tilgang og et ønske om å kommentere
på emne ha muligheten om det skulle være gjest gjennom gjeste-kode, en student som tar emnet eller en foreleser
for foreleser i emnet (evt. admin som har tilgang til alle emner). Selv om alle kommentarer sendt av innloggede 
brukere knyttes til brukeren og lagres i databasen vil studenter og gjester som kommenterer forbli anonyme for
andre brukergrupper utenom admin.

Disse meldingene struktureres i et nestet samtale-mønster i databasen før visning for å gi støtte til samtaler
hvor svar kan bygges på svar. 

### En foreleser skal
#### Kunne registrere seg
En ansatt registrerer seg i ansatt_signup.php. Her finner man mye likt som ved student_signup.php, men med
støtte for opplasting av bilder og et emne man er ansvarlig for velges i stedet. Forelesere får
rollen 'Lærer (Inaktiv)' etter registrering og trenger godkjenning fra en Admin gjennom admin-panelet
for å bli satt som aktiv, noe som tillater kommentering med navn og bilde på emner.

#### Under registrering angi hvilke emner man underviser.
Under registrerings-prosessen som lærer/foreleser kan man velge hvilket fag man underviser i.
For simpelhetens skyld tok vi valget om at det kun er mulig å registrere seg i ett emne om gangen,
men databasen har støtte for flere - og tillater derfor endringen hvis det er noe vi vil endre senere.

#### Kunne logge inn/bytte passord/glemt passord
Ref: En student skal kunne bytte passord / utføre "glemt passord".
Disse funksjonene fungerer likt for alle brukergrupper. 

#### Lese meldinger fra studenter i emne man underviser i
Gjennom emne.php har lærere tilgang til å lese anonyme meldinger sendt av studenter i
de respektive emnene som de foreleser i.

#### Svare på meldinger fra studenter.
Lærere har på lik linje med gjester og studenter muligheten til å kommentere og svare på meldinger ved å bruke
funksjonene i emne.php, men det er kun de med den verifiserte lærer-rollen som kan vil dukke opp med
navn og bilde.

### En gjestbruker skal
#### Kunne se og lese alle meldinger og svar for et valgt emne
Alle emner (og derfor rom) har en predefinert kode som alle kan benytte ved å gå under "gjestebruker" på
hoved-siden. De får da en midlertidig tilgang til rommet og kan lese emnekode og samtaler med vanlig
anonymisert tilgang.

#### Kunne rapportere en upassende melding.
Gjestebrukere kan på lik linje med andre brukere av systemet rapportere meldinger de finner
upassende. Dette gjøres ved å trykke på en knapp som åpner et kommentar-felt hvor brukeren får
muligheten til å forklare sin rapportering. Dette blir da lagret i databasen og vil videre
vises på Admin-siden hvor en admin kan håndtere rapporteringen.

#### Kunne legge inn en kommentar
Gjestebrukere kan også, på lik linje med andre brukere som har tilgang til et emne, legge inn en kommentar
som vil bli lagret i databasen. Forskjellen er at en kommentar fra en gjestebruker vil være sann-annonymisert
og vil ikke inneholde noen informasjon om brukeren.

### En administrator skal
#### Logge inn 
Logger seg inn ved samme måte som andre brukere. Administrator har rolle som gir de spesielle tilgang.
#### Kunne finne ut hvem som har sendt en melding
Dette er støttet i databasen med et kall som gir samtale treet på lik linje
som i emne, men med deanonymiserte datafelt. Dersom bruker er admin som sjekkes i check.php filen, vil de få tilgang til deanonymert kommentarer.
#### Godkjenne brukerregistreringer som forelesere gjør før de blir "aktive".
Denne funksjonen er tilgjengelig i admin.php, der administrator kan godkjenne uverifserte forelesere. Dersom admin godkjenner foreleser, blir det utført et SQL kall, for å oppdatere foreleserens rolle. Admin har også muligheten til å avvise, der brukeren blir sletta.
#### Slette/endre studentbrukere og ansatt brukere
I admin.php blir alle studenter og forelesere listet opp med SQL kall. En administrator kan da trykke på Endre knappen for å utføre endringer i admin_action.php. For studenter, kan en admin endre navn, e-post, passord, semester og studieretning. For forelesere, kan admin bare endre navn, e-post og passord. Foreleserens emne blir også listet opp her. Endringene av brukere blir utført av SQL kall ved hjelp av php, som også henter den valgte brukerens id fra html form i admin.php siden.
#### Slette meldinger og svar
I emne.php siden kan man slette meldinger og svar dersom man er en admin bruker.
#### Få en oversikt over rapporterte meldinger.
Rapporterte meldinger kan vises/slettes via admin.php. Her får admin se hvilke meldinger som ble rapportert, eieren av den rapporterte meldingen, og hvem som har rapportert meldingen med en forklarende tekst på hvorfor de rapporterte kommentaren.
### I tillegg skal en app utvikles
#### App
Utvikles for øyeblikket, detaljer kommer når dette er relevant.
#### API
Punktene for å handle som en innlogget bruker (uten om passord management) er støttes
se dokumentasjon under API_docs. Disse benyter en 30 minutters UUID token som lagres av klienten for persistering
av tilgang.
#### Dokumentasjon
Finnes under /dokumentasjon/API_docs.html

