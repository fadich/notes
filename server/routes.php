<?php

use \Psr\Http\Message\ServerRequestInterface;

function ping () {
    $args = func_get_args();
    /** @var \RequestHandler $handler */
    $handler = end($args);

    return RequestHandler::response(['OK'], 200, $handler->headers);
}

function search(ServerRequestInterface $request, Repository $repository, RequestHandler $handler) {
    $params = $request->getQueryParams();
    $query = $params['query'] ?? '';

    $page = (int)($params['page'] ?? 1);
    $page = ($page < 1) ? 1 : $page;

    $perPage = (int)($params['per-page'] ?? 9);
    $perPage = ($perPage < 1) ? 1 : $perPage;
    $perPage = ($perPage > 100) ? 100 : $perPage;

    $items = $repository->search($query, $page, $perPage);

    return RequestHandler::response([
        $items,
    ], 200, $handler->headers);
}

function save(ServerRequestInterface $request, RequestHandler $handler) {
    $body = $request->getParsedBody();
    if (!isset($body['data'])) {
        return RequestHandler::response([
            'success' => false,
            'message' => 'Data does not set',
        ], 400, $handler->headers);
    }

    if (json_decode($body['data']) === null) {
        return RequestHandler::response([
            'success' => false,
            'message' => 'Invalid data',
        ], 406, $handler->headers);
    }

    file_put_contents('storage.json', $body['data']);
    return RequestHandler::response([
        'success' => false,
        'message' => 'Saved',
    ], 200, $handler->headers);
}

function get(ServerRequestInterface $request, RequestHandler $handler) {
    $data = file_get_contents('storage.json');
    return RequestHandler::response([
        'data' => $data,
    ], 200, $handler->headers);
}

function insert(ServerRequestInterface $request, Repository $repository, RequestHandler $handler) {
    $body = $request->getParsedBody();
    if (!isset($body['title']) || !$body['title']) {
        return RequestHandler::response([
            'success' => false,
            'message' => 'Field "title" required',
        ], 400, $handler->headers);
    }

    $repository->addNote($body['title'], $body['content'] ?? '')->save();

    return RequestHandler::response([
        'success' => true,
        'message' => 'Successfully created',
        'item' => $body,
    ], 200, $handler->headers);
}

function update(ServerRequestInterface $request, int $id, Repository $repository, RequestHandler $handler) {
    $body = $request->getParsedBody();
    if (!isset($body['title']) || !$body['title']) {
        return RequestHandler::response([
            'success' => false,
            'message' => 'Field "title" required',
        ], 400, $handler->headers);
    }

    try {
        $repository->updateNote($id, $body['title'], $body['content'] ?? '')->save();
    } catch (Exception $e) { // CATCH
        return RequestHandler::response([
            'success' => false,
            'message' => $e->getMessage(),
        ], 400, $handler->headers);
    }

    return RequestHandler::response([
        'success' => true,
        'message' => 'Successfully updated',
        'item' => $body,
    ], 200, $handler->headers);
}

function delete(ServerRequestInterface $request, int $id, Repository $repository, RequestHandler $handler) {
    try {
        $repository->deleteNote($id)->save();
    } catch (Exception $e) { // CATCH
        return RequestHandler::response([
            'success' => false,
            'message' => $e->getMessage(),
        ], 400, $handler->headers);
    }

    return RequestHandler::response(['Deleted'], 200, $handler->headers);
}

function deleteNew(ServerRequestInterface $request, Repository $repository, RequestHandler $handler) {
    $params = $request->getQueryParams();
    $id = $params['id'] ?? null;

    if ($id === null) {
        return RequestHandler::response([
            'success' => false,
            'message' => 'ID does not specified',
        ], 400, $handler->headers);
    }

    try {
        $repository->deleteNote($id)->save();
    } catch (Exception $e) { // CATCH
        return RequestHandler::response([
            'success' => false,
            'message' => $e->getMessage(),
        ], 400, $handler->headers);
    }

    return RequestHandler::response(['Deleted'], 200, $handler->headers);
}
