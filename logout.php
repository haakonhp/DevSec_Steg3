<?php

// Når man trykker på logout knappen, havner man på denne logout pagen, og da vil vi destroye session.
session_start();
session_destroy();

// Videre vil den bare sende oss tilbake til index siden, så man merker nesten ikke at man har vært i en logout side.
header("Location: index.php");
exit;