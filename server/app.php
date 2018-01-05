<?php

include 'Repository.php';

$rep = new Repository(2,2);

hint();

while (1) {
    $insert = readline('Query:');
    switch ($insert) {
        case '\i':
            insert($rep);
            break;
        case '\h':
            hint();
            break;
        default:
            $list = $rep->search($insert);
            viewList($list);
    }
}

function writeln(string $message = '') {
    echo "{$message}\n";
}

function hint() {
    writeln("There are simple commands which can be used.");
    writeln();
    writeln("\i\t-\tInsert new note.");
    writeln("\s\t-\tSearch for note(-s).");
    writeln("Or just type a query...");
}

function insert(Repository $rep) {
    $insert = readline('Inset note ("title [|| content]):');
    $sep = explode('||', $insert);
    $rep->addNote(
        trim($sep[0]),
        trim($sep[1] ?? '')
    )->save();

    writeln("Note saved!");
}

function viewList(array $list) {
    $row = function (array $fields) {
        $id = $fields[0] ?? $fields['id'] ?? '--';
        $title = $fields[1] ?? $fields['title'] ?? '--';
        $content = $fields[2] ?? $fields['content'] ?? '--';
        $score = $fields[3] ?? $fields['_score'] ?? '--';

        writeln("{$id}\t\t{$title}\t\t{$content}\t\t{$score}");
    };

    $row([
        'ID',
        'Title',
        'Content',
        'Score',
    ]);

    foreach ($list as $item) {
        $row($item);
    }
}
