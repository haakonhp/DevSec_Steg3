# API Steg 3

## loginGetToken
    Parameters: $email, $password Delivery: POST via form-data
$email: E-post til en allerede registrert bruker. Post parameter er nødvendig,
og må leveres med samme navn som parameter kravet.

$password: Passordet til en allerede registrert bruker. Post parameter er nødvendig,
og må leveres med samme navn som parameter kravet.

$response: En 25-tengs tekststreng som representerer bruker en gitt ved en gyldig e-post + passord kombinasjon.
Dette er en 30 minutters token som benyttes som identifisering til
de andre api punktene, og handlingene knyttes til den registrerte brukeren.
Denne tokenen utgår etter 30 minutter, og må lagres av klienten for
fremtidige requests.

## getUserInfo
    Parameters: $auth_token Delivery: POST via form-data
$auth_token: Viser til en 25-tegn lang UUID som blir produsert via
loginGetToken API punktet. Denne tokene er tilknyttet en bruker,
og handlingen og følgelig resultatet av API kallet vil avhenge
av den innloggede brukeren.

$response: Et assosiativt array med informasjon om brukeren som bilde, navn, og e-post.

## getRoles
    Parameters: $auth_token Delivery: POST via form-data
$auth_token: Viser til en 25-tegn lang UUID som blir produsert via
loginGetToken API punktet. Denne tokene er tilknyttet en bruker,
og handlingen og følgelig resultatet av API kallet vil avhenge
av den innloggede brukeren.

$response: Et assosiativt array av brukerens roller. Gir rolle_id
og rollenavn.

## getSubjects
    Parameters: $auth_token Delivery: POST via form-data
$auth_token: Viser til en 25-tegn lang UUID som blir produsert via
loginGetToken API punktet. Denne tokenen er tilknyttet en bruker,
og handlingen og følgelig resultatet av API kallet vil avhenge
av den innloggede brukeren.

$response: Et assosiativt array av brukerens fag. Denne inneholder blant annet den
tre sifrede tegnkoden til fagene brukeren kan ønske å hente via getRoom. Videre inkluderes
navnet til faget, og eventuelt en lenke til siden for visning via nettleser.

## getRoom
    Parameters: $auth_token, $room Delivery: POST via form-data
$auth_token: Viser til en 25-tegn lang UUID som blir produsert via
loginGetToken API punktet. Denne tokenen er tilknyttet en bruker,
og handlingen og følgelig resultatet av API kallet vil avhenge
av den innloggede brukeren.

$room viser til en gyldig 3 tegns kode for et gitt rom. Dette
rommet må i tillegg være et gyldig rom for brukeren som førespørselen
utføres som.

$response: Hele samtalen i rommet i form av et assosiativt array levert via
JSON. Rekkefølgen på meldingene leveres slik at en toppkommentar
etterfølges av svar (og eventuelt deres svar) i en "nøstet struktur".
Her tilbys også en depth verdi for hver kommentar, for å gi en måte
å enkelt vise grafisk hvilke kommentarer er svar, og til hvem.

Et eksempel på en slik kommentar felt følger under:

+ Dybde 0: Toppkommentar
  + Dybde 1: Svar til toppkommentar
    + Dybde 2: Svar til dybde 1 kommentar
  + Dybde 1: Svar til toppkommentar
+ Dybde 0: Ny toppkommentar

## sendComment
    Parameters: $auth_token, $text, $room_id, $reply_id:optional $room Delivery: POST via form-data
$auth_token: Viser til en 25-tegn lang UUID som blir produsert via
loginGetToken API punktet. Denne tokenen er tilknyttet en bruker,
og handlingen og følgelig resultatet av API kallet vil avhenge
av den innloggede brukeren.

$text: En streng som representerer hele kommentaren.

$room_id: en 3 tegn kode for et gitt rom. 

$reply_id: ID-en til kommentaren man ønsker å svare på. Gitt i response
av getRoom, kan legges tom hvis en ønsker å produsere en topp nivå 
kommentar.

$response: Enten "Successfully posted" ved vellykket operasjon, eller
ingen responser ved feil.

