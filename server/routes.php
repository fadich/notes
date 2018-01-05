<?php

use \Psr\Http\Message\ServerRequestInterface;

function search(ServerRequestInterface $request)
{
    return RequestHandler::response(['Search.', func_get_args()]);
}

function insert(ServerRequestInterface $request)
{
    return RequestHandler::response(['Insert.', func_get_args()]);
}

function update(ServerRequestInterface $request, int $id)
{
    return RequestHandler::response(['Update.', func_get_args()]);
}


function delete(ServerRequestInterface $request, int $id)
{
    return RequestHandler::response(['Delete.', func_get_args()]);
}
