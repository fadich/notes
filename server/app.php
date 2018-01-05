<?php

include 'Repository.php';

$rep = new Repository();

while (1) {
    $insert = readline('Inset note ("title [|| content]):');
    $sep = explode('||', $insert);
    $rep->addNote(
        trim($sep[0]),
        trim($sep[1] ?? '')
    )->save();

    echo "\nNote saved!\n";
}
